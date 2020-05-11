<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use function abs;
use App\Controller\EasyAdmin\AbstractController;
use App\Doctrine\Registry;
use App\Event\PartAccrued;
use App\Event\PartCreated;
use App\Event\PartDecreased;
use App\Form\Type\QuantityType;
use App\Manager\DeficitManager;
use App\Manager\PartManager;
use App\Manager\ReservationManager;
use App\Manufacturer\Domain\Manufacturer;
use App\Order\Entity\Order;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
use App\Part\Domain\PartId;
use App\Part\Domain\Stockpile;
use App\Part\Form\PartDto;
use App\Roles;
use App\State;
use App\Storage\Entity\Motion;
use App\Storage\Entity\MotionManual;
use App\Vehicle\Domain\Model;
use function array_keys;
use function array_map;
use function assert;
use Closure;
use function count;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use function explode;
use function implode;
use LogicException;
use function mb_strtolower;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use function sprintf;
use function str_ireplace;
use function str_replace;
use function strpos;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints;
use function trim;
use function urldecode;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AbstractController
{
    private DeficitManager $deficitManager;

    private PartManager $partManager;

    private MoneyFormatter $formatter;

    private ReservationManager $reservationManager;

    public function __construct(
        DeficitManager $deficitManager,
        PartManager $partManager,
        MoneyFormatter $formatter,
        ReservationManager $reservationManager
    ) {
        $this->deficitManager = $deficitManager;
        $this->partManager = $partManager;
        $this->formatter = $formatter;
        $this->reservationManager = $reservationManager;
    }

    public function crossAction(): Response
    {
        $left = $this->findCurrentEntity();

        if (!$left instanceof Part) {
            throw new LogicException('Parts required.');
        }

        $form = $this->createFormBuilder()
            ->add('right', EasyAdminAutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Аналог',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotEqualTo(['value' => $left]),
                ],
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->partManager->cross($left, $form->get('right')->getData());

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/cross.html.twig', [
            'part' => $left,
            'form' => $form->createView(),
        ]);
    }

    public function uncrossAction(): Response
    {
        $part = $this->findCurrentEntity();
        if (!$part instanceof Part) {
            throw new LogicException('Parts required.');
        }

        $this->partManager->uncross($part);

        return $this->redirectToReferrer();
    }

    public function stockAction(): Response
    {
        $registry = $this->container->get(Registry::class);

        $quantities = $registry->repository(Motion::class)
            ->createQueryBuilder('motion', 'motion.part.id')
            ->select('SUM(motion.quantity) AS quantity, motion.part.id AS part_id')
            ->groupBy('motion.part.id')
            ->having('SUM(motion.quantity) <> 0')
            ->getQuery()
            ->getArrayResult();

        $parts = $registry->repository(Part::class)->createQueryBuilder('part')
            ->select('part')
            ->where('part.id IN (:ids)')
            ->orderBy('part.id')
            ->getQuery()
            ->setParameter('ids', array_keys($quantities))
            ->getResult();

        return $this->render('easy_admin/part/stock.html.twig', [
            'parts' => $parts,
            'quantities' => $quantities,
        ]);
    }

    public function deficitAction(): Response
    {
        return $this->render('easy_admin/part/deficit.html.twig', [
            'parts' => $this->deficitManager->findDeficit(),
        ]);
    }

    public function incomeAction(): Response
    {
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registry = $this->container->get(Registry::class);

            $em = $registry->manager(MotionManual::class);
            $quantity = abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = sprintf('# Ручное пополнение - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, $quantity, $description));
            $em->flush();

            $this->event(new PartAccrued($part, [
                'quantity' => $quantity,
            ]));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/income.html.twig', [
            'part' => $part,
            'form' => $form->createView(),
        ]);
    }

    public function outcomeAction(): Response
    {
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registry = $this->container->get(Registry::class);

            $em = $registry->manager(MotionManual::class);
            $quantity = abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = sprintf('# Ручное списание - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, 0 - $quantity, $description));
            $em->flush();

            $this->event(new PartDecreased($part, [
                'quantity' => $quantity,
            ]));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/outcome.html.twig', [
            'part' => $part,
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $entity = $parameters['entity'];
            assert($entity instanceof Part);

            $parameters['inStock'] = $this->partManager->inStock($entity);
            $parameters['orders'] = $this->partManager->inOrders($entity);
            $parameters['reservedIn'] = array_map(
                fn (Order $order): int => (int) $order->getId(),
                $this->reservationManager->orders($entity)
            );
            $parameters['reserved'] = $this->reservationManager->reserved($entity);
            $parameters['crosses'] = $this->partManager->getCrosses($entity);

            $registry = $this->container->get(Registry::class);

            $parameters['carModels'] = $registry->repository(Model::class)
                ->createQueryBuilder('carModel')
                ->join(PartCase::class, 'partCase', Join::WITH, 'carModel.uuid = partCase.vehicleId')
                ->where('partCase.partId = :part')
                ->setParameter('part', $entity->toId())
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityFormBuilder($entity, $view): FormBuilder
    {
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if ('edit' === $view) {
            $formBuilder->get('number')->setDisabled(!$this->isGranted(Roles::SUPER_ADMIN));
        }

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $isPlusExist = false !== strpos($searchQuery, '+');
        if ($isPlusExist) {
            $searchQuery = str_replace('+', '', $searchQuery);
        }

        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->join(Manufacturer::class, 'manufacturer', Join::WITH, 'manufacturer.uuid = part.manufacturerId');

        $cases = [];

        if (!$isPlusExist) {
            /** @var Model[] $cases */
            $registry = $this->container->get(Registry::class);

            $cases = $registry->repository(Model::class)
                ->createQueryBuilder('entity')
                ->select('PARTIAL entity.{id, uuid, caseName}')
                ->where('entity.caseName IN (:cases)')
                ->getQuery()
                ->setParameter('cases', explode(' ', trim($searchQuery)))
                ->getResult();

            $carModel = $this->getEntity(Model::class);

            if ($carModel instanceof Model) {
                $cases[] = $carModel;
            }
        }

        if (0 < count($cases)) {
            $request = $this->request;

            if (!$request->isXmlHttpRequest()) {
                $this->addFlash(
                    'info',
                    sprintf('Поиск по кузовам "%s"', implode(',', $cases))
                );
            }

            foreach ($cases as $case) {
                assert($case instanceof Model);

                $searchQuery = str_ireplace($case->caseName, '', $searchQuery);
            }
            $searchQuery = str_replace('  ', ' ', $searchQuery);

            $qb
                ->leftJoin(PartCase::class, 'pc', Join::WITH, 'pc.partId = part.partId')
                ->where($qb->expr()->orX(
                    $qb->expr()->in('pc.vehicleId', ':cases'),
                    $qb->expr()->eq('part.universal', ':universal')
                ))
                ->setParameters([
                    'cases' => array_map(fn (Model $model) => $model->toId(), $cases),
                    'universal' => true,
                ]);
        }

        foreach (explode(' ', trim($searchQuery)) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(part.name)', $key),
                $qb->expr()->like('LOWER(part.number)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower(trim($searchString)).'%');
        }

        $state = $this->container->get(State::class);
        $qb->leftJoin(
            Stockpile::class,
            'stockpile',
            Join::WITH,
            'stockpile.part = part AND stockpile.tenant = :tenant'
        )
            ->setParameter('tenant', $state->tenant())
            ->groupBy('part.id')
            ->addSelect('SUM(stockpile.quantity) as HIDDEN stock')
            ->addSelect('CASE WHEN SUM(stockpile.quantity) IS NULL THEN 1 ELSE 0 END as HIDDEN null_stock')
            ->orderBy('null_stock', 'ASC')
            ->addOrderBy('stock', 'DESC');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $queryString = str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $carModel = $this->getEntity(Model::class);
        $useCarModelInFormat = false === strpos($queryString, '+');

        $normalizer = function (Part $entity, bool $analog = false) use ($carModel, $useCarModelInFormat): array {
            $text = sprintf(
                '%s (Склад: %s) | %s',
                $this->display($entity->toId()),
                $this->partManager->inStock($entity) / 100,
                $this->formatter->format($entity->price),
            );

            if ($carModel instanceof Model && $useCarModelInFormat && !$entity->universal) {
                $text = sprintf('[%s] %s', $this->display($carModel->toId()), $text);
            }

            if ($analog) {
                $text = ' [АНАЛОГ] '.$text;
            }

            return [
                'id' => $entity->getId(),
                'text' => $text,
            ];
        };

        $data = [];
        if (3 >= $paginator->getNbResults()) {
            foreach ($paginator->getCurrentPageResults() as $part) {
                /* @var $part Part */
                $data[] = $normalizer($part);

                foreach ($this->partManager->getCrosses($part) as $cross) {
                    if ($cross->getId() === $part->getId()) {
                        continue;
                    }

                    if (0 < $this->partManager->inStock($cross)) {
                        $data[] = $normalizer($cross, true);
                    }
                }
            }
        } else {
            $data = array_map($normalizer, (array) $paginator->getCurrentPageResults());
        }

        return $this->json(['results' => $data]);
    }

    protected function createNewEntity(): PartDto
    {
        return $this->createWithoutConstructor(PartDto::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $model = $entity;
        assert($model instanceof PartDto);

        $entity = new Part(
            PartId::generate(),
            $model->manufacturer->toId(),
            $model->name,
            $model->number,
            $model->universal,
            $model->price,
            $model->discount
        );

        try {
            parent::persistEntity($entity);
        } catch (UniqueConstraintViolationException $e) {
            // TODO Написать нормальный валидатор для модели
            $this->addFlash('error', sprintf('Запчасть %s у %s уже существует!', $model->number, $this->display($model->manufacturer->toId())));

            return;
        }

        $referer = $this->request->query->get('referer');
        if (null !== $referer) {
            $this->setReferer(urldecode($referer).'&part_id='.$entity->getId());
        }

        $this->event(new PartCreated($entity));
    }

    protected function createEditDto(Closure $closure): ?object
    {
        $registry = $this->container->get(Registry::class);

        $arr = $closure();

        return new PartDto(
            $arr['partId'],
            $registry->findBy(Manufacturer::class, ['uuid' => $arr['manufacturerId']]),
            $arr['name'],
            $arr['number'],
            new Money($arr['price.amount'], new Currency($arr['price.currency.code'])),
            $arr['universal'],
            new Money($arr['discount.amount'], new Currency($arr['discount.currency.code'])),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): Part
    {
        $registry = $this->container->get(Registry::class);

        $dto = $entity;
        assert($dto instanceof PartDto);

        /** @var Part $entity */
        $entity = $registry->findBy(Part::class, ['partId' => $dto->partId]);

        $entity->update(
            $dto->name,
            $dto->universal,
            $dto->price,
            $dto->discount,
        );

        parent::updateEntity($entity);

        if ($dto->universal) {
            $registry->repository(PartCase::class)
                ->createQueryBuilder('entity')
                ->delete()
                ->where('entity.partId = :part')
                ->setParameter('part', $entity->toId())
                ->getQuery()
                ->execute();
        }

        return $entity;
    }

    protected function caseAction(): Response
    {
        /** @var Part|null $part */
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new BadRequestHttpException('Part required.');
        }

        $dto = new PartCaseDTO($part);

        $form = $this->createFormBuilder($dto)
            ->add('part', EasyAdminAutocompleteType::class, [
                'label' => 'Запчасть',
                'class' => Part::class,
                'disabled' => true,
            ])
            ->add('vehicle', EasyAdminAutocompleteType::class, [
                'label' => 'Модель',
                'class' => Model::class,
                'help' => 'Проивзодитель, Модель, Год, Поколение, Комплектация, Лошадинные силы',
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->container->get(Registry::class)->manager(PartCase::class);
            $em->persist(new PartCase($dto->part->toId(), $dto->vehicle->toId()));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/default/new.html.twig', [
            'form' => $form->createView(),
            'entity_fields' => [],
            'entity' => $dto,
        ]);
    }
}
