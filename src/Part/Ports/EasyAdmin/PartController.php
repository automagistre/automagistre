<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use function abs;
use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Form\Type\QuantityType;
use App\Manufacturer\Domain\Manufacturer;
use App\Order\Entity\Order;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
use App\Part\Domain\PartId;
use App\Part\Domain\PartNumber;
use App\Part\Domain\Stockpile;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartCreated;
use App\Part\Event\PartDecreased;
use App\Part\Form\PartDto;
use App\Part\Manager\DeficitManager;
use App\Part\Manager\PartManager;
use App\State;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use App\Storage\Manager\ReservationManager;
use App\Vehicle\Domain\Model;
use function array_keys;
use function array_map;
use function assert;
use Closure;
use function count;
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
            ->add('right', AutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Аналог',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotEqualTo(['value' => $left->toId()]),
                ],
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->partManager->cross($left->toId(), $form->get('right')->getData());

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

        $this->partManager->uncross($part->toId());

        return $this->redirectToReferrer();
    }

    public function stockAction(): Response
    {
        $quantities = $this->registry->repository(Motion::class)
            ->createQueryBuilder('motion', 'motion.part.id')
            ->select('SUM(motion.quantity) AS quantity, motion.part.id AS part_id')
            ->groupBy('motion.part.id')
            ->having('SUM(motion.quantity) <> 0')
            ->getQuery()
            ->getArrayResult();

        $parts = $this->registry->repository(Part::class)->createQueryBuilder('part')
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
            $em = $this->registry->manager(Motion::class);
            $quantity = abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = sprintf('# Ручное пополнение - %s', $user->getId());

            $em->persist(new Motion($part->toId(), $quantity, Source::manual(), $user->toId()->toUuid(), $description));
            $em->flush();

            $this->event(new PartAccrued($part->toId(), [
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
            $em = $this->registry->manager(Motion::class);
            $quantity = abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = sprintf('# Ручное списание - %s', $user->getId());

            $em->persist(new Motion($part->toId(), 0 - $quantity, Source::manual(), $user->toId()->toUuid(), $description));
            $em->flush();

            $this->event(new PartDecreased($part->toId(), [
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
            $part = $parameters['entity'];
            assert($part instanceof Part);

            $parameters['inStock'] = $this->partManager->inStock($part->toId());
            $parameters['orders'] = $this->partManager->inOrders($part->toId());
            $parameters['reservedIn'] = array_map(
                fn (Order $order): int => (int) $order->getId(),
                $this->reservationManager->orders($part->partId)
            );
            $parameters['reserved'] = $this->reservationManager->reserved($part->toId());
            $parameters['crosses'] = $this->partManager->getCrosses($part->toId());

            $parameters['carModels'] = $this->registry->repository(Model::class)
                ->createQueryBuilder('carModel')
                ->join(PartCase::class, 'partCase', Join::WITH, 'carModel.uuid = partCase.vehicleId')
                ->where('partCase.partId = :part')
                ->setParameter('part', $part->toId())
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_ARRAY);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
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
            $cases = $this->registry->repository(Model::class)
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
            $numberKey = $key.'_number';

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(part.name)', $key),
                $qb->expr()->like('part.number', $numberKey),
                $qb->expr()->like('LOWER(manufacturer.name)', $key)
            ));

            $qb
                ->setParameter($numberKey, '%'.PartNumber::sanitize($searchString).'%')
                ->setParameter($key, '%'.mb_strtolower(trim($searchString)).'%');
        }

        $state = $this->container->get(State::class);
        $qb->leftJoin(
            Stockpile::class,
            'stockpile',
            Join::WITH,
            'stockpile.partId = part.partId AND stockpile.tenant = :tenant'
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
        $isUuid = $query->has('use_uuid');

        $queryString = str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $carModel = $this->getEntity(Model::class);
        $useCarModelInFormat = false === strpos($queryString, '+');

        $normalizer = function (Part $part, bool $analog = false) use (
            $carModel,
            $useCarModelInFormat,
            $isUuid
        ): array {
            $text = sprintf(
                '%s (Склад: %s) | %s',
                $this->display($part->toId()),
                $this->partManager->inStock($part->toId()) / 100,
                $this->formatter->format($part->price),
            );

            if ($carModel instanceof Model && $useCarModelInFormat && !$part->universal) {
                $text = sprintf('[%s] %s', $this->display($carModel->toId()), $text);
            }

            if ($analog) {
                $text = ' [АНАЛОГ] '.$text;
            }

            return [
                'id' => $isUuid ? $part->toId()->toString() : $part->getId(),
                'text' => $text,
            ];
        };

        $data = [];
        if (3 >= $paginator->getNbResults()) {
            foreach ($paginator->getCurrentPageResults() as $part) {
                /* @var $part Part */
                $data[] = $normalizer($part);

                foreach ($this->partManager->getCrosses($part->toId()) as $cross) {
                    if ($cross->getId() === $part->getId()) {
                        continue;
                    }

                    if (0 < $this->partManager->inStock($cross->partId)) {
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
    protected function persistEntity($entity): Part
    {
        $model = $entity;
        assert($model instanceof PartDto);

        $entity = new Part(
            PartId::generate(),
            $model->manufacturerId,
            $model->name,
            new PartNumber($model->number),
            $model->universal,
            $model->price,
            $model->discount
        );

        parent::persistEntity($entity);

        $referer = $this->request->query->get('referer');
        if (null !== $referer) {
            $this->setReferer(urldecode($referer).'&part_id='.$entity->getId());
        }

        $this->event(new PartCreated($entity));

        return $entity;
    }

    protected function createEditDto(Closure $closure): ?object
    {
        $arr = $closure();

        return new PartDto(
            $arr['partId'],
            $arr['manufacturerId'],
            $arr['name'],
            $arr['number']->number,
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
        $dto = $entity;
        assert($dto instanceof PartDto);

        /** @var Part $entity */
        $entity = $this->registry->findBy(Part::class, ['partId' => $dto->partId]);

        $entity->update(
            $dto->name,
            $dto->universal,
            $dto->price,
            $dto->discount,
        );

        parent::updateEntity($entity);

        if ($dto->universal) {
            $this->registry->repository(PartCase::class)
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
            $em = $this->registry->manager(PartCase::class);
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
