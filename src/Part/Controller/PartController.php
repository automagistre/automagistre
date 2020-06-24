<?php

declare(strict_types=1);

namespace App\Part\Controller;

use function abs;
use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Form\Type\QuantityType;
use App\Order\Entity\Order;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\Part;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Part\Entity\PartView;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartCreated;
use App\Part\Event\PartDecreased;
use App\Part\Form\PartCaseDTO;
use App\Part\Form\PartDto;
use App\Part\Manager\DeficitManager;
use App\Part\Manager\PartManager;
use App\PartPrice\Entity\Discount;
use App\PartPrice\Entity\Price;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use function array_diff;
use function array_keys;
use function array_map;
use function array_unique;
use function array_values;
use function assert;
use Closure;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use LogicException;
use function mb_strtoupper;
use function sprintf;
use function str_replace;
use function strpos;
use function strtoupper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    private ReservationManager $reservationManager;

    public function __construct(
        DeficitManager $deficitManager,
        PartManager $partManager,
        ReservationManager $reservationManager
    ) {
        $this->deficitManager = $deficitManager;
        $this->partManager = $partManager;
        $this->reservationManager = $reservationManager;
    }

    public function crossAction(): Response
    {
        $left = $this->findCurrentEntity();

        if (!$left instanceof PartView) {
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
        if (!$part instanceof PartView) {
            throw new LogicException('Parts required.');
        }

        $this->partManager->uncross($part->toId());

        return $this->redirectToReferrer();
    }

    public function stockAction(): Response
    {
        $parts = $this->registry->repository(PartView::class)
            ->createQueryBuilder('part')
            ->select('part')
            ->where('part.quantity > 0')
            ->orderBy('part.id')
            ->getQuery()
            ->getResult();

        return $this->render('easy_admin/part/stock.html.twig', [
            'parts' => $parts,
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

    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        $this->entity['class'] = PartView::class;

        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'] ?? null;
        if ($entity instanceof Part) {
            $easyadmin['item'] = $this->registry->getBy(PartView::class, ['id' => $entity->toId()]);
            $request->attributes->set('easyadmin', $easyadmin);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $part = $parameters['entity'];
            assert($part instanceof PartView);

            $parameters['inStock'] = $this->partManager->inStock($part->toId());
            $parameters['orders'] = $this->partManager->inOrders($part->toId());
            $parameters['reservedIn'] = array_map(
                fn (Order $order): int => (int) $order->getId(),
                $this->reservationManager->orders($part->toId())
            );
            $parameters['reserved'] = $this->reservationManager->reserved($part->toId());
            $parameters['crosses'] = $this->partManager->getCrosses($part->toId());

            $parameters['prices'] = $this->registry->viewListBy(
                Price::class,
                ['partId' => $part->toId()],
                ['since' => 'DESC'],
            );
            $parameters['discounts'] = $this->registry->viewListBy(
                Discount::class,
                ['partId' => $part->toId()],
                ['since' => 'DESC'],
            );

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

        $qb = $this->em->getRepository(PartView::class)
            ->createQueryBuilder('part')
            ->orderBy('part.'.$sortField, $sortDirection);

        $vehicleId = $this->getIdentifier(VehicleId::class);

        if (!$isPlusExist && $vehicleId instanceof VehicleId) {
            $carModel = $this->registry->getBy(Model::class, ['uuid' => $vehicleId]);

            if (null !== $carModel->caseName) {
                if (!$this->request->isXmlHttpRequest()) {
                    $this->addFlash(
                        'info',
                        sprintf('Поиск по кузову "%s"', $carModel->caseName)
                    );
                }

                $qb
                    ->where($qb->expr()->orX(
                        $qb->expr()->like('part.cases', ':case'),
                        $qb->expr()->eq('part.isUniversal', ':universal')
                    ))
                    ->setParameters([
                        'case' => strtoupper($carModel->caseName),
                        'universal' => true,
                    ]);
            }
        }

        foreach (explode(' ', trim($searchQuery)) as $key => $searchString) {
            $key = ':search_'.$key;
            $numberKey = $key.'_number';

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('part.search', $key),
                $qb->expr()->like('part.number', $numberKey),
            ));

            $qb
                ->setParameter($numberKey, '%'.PartNumber::sanitize($searchString).'%')
                ->setParameter($key, '%'.mb_strtoupper(trim($searchString)).'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $queryString = str_replace(['.', ',', '-', '_'], '', (string) $query->get('query'));
        $qb = $this->createSearchQueryBuilder((string) $query->get('entity'), $queryString, [])
            ->orderBy('part.quantity', 'DESC');

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->getInt('page', 1));

        $vehicleId = $this->getIdentifier(VehicleId::class);
        $useCarModelInFormat = false === strpos($queryString, '+');

        $normalizer = function (PartView $part, bool $analog = false) use ($vehicleId, $useCarModelInFormat): array {
            $text = sprintf(
                '%s (Склад: %s) | %s',
                $part->display(),
                $part->quantity / 100,
                $this->formatMoney($part->sellPrice()),
            );

            if ($vehicleId instanceof VehicleId && $useCarModelInFormat && !$part->isUniversal) {
                $text = sprintf('[%s] %s', $this->display($vehicleId), $text);
            }

            if ($analog) {
                $text = ' [АНАЛОГ] '.$text;
            }

            return [
                'id' => $part->id->toString(),
                'text' => $text,
            ];
        };

        $data = [];
        $analogs = [];
        if (3 >= $paginator->getNbResults()) {
            foreach ($paginator->getCurrentPageResults() as $part) {
                /* @var $part PartView */
                $data[$part->toId()->toString()] = $normalizer($part);

                $analogs = [...$analogs, ...$part->analogs];
            }
        } else {
            $data = array_map($normalizer, (array) $paginator->getCurrentPageResults());
        }

        if ([] !== $analogs) {
            $analogs = $this->registry->manager(PartView::class)
                ->createQueryBuilder()
                ->select('entity')
                ->from(PartView::class, 'entity')
                ->where('entity.id IN (:ids)')
                ->andWhere('entity.quantity > 0')
                ->getQuery()
                ->setParameter('ids', array_diff(array_unique($analogs), array_keys($data)))
                ->getResult();

            foreach ($analogs as $analog) {
                $data[] = $normalizer($analog);
            }
        }

        return $this->json(['results' => array_values($data)]);
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

        $partId = PartId::generate();
        $entity = new Part(
            $partId,
            $model->manufacturerId,
            $model->name,
            new PartNumber($model->number),
            $model->universal,
        );

        parent::persistEntity($entity);

        $tenant = $this->registry->manager(Price::class);
        $tenant->persist(new Price($partId, $model->price, new DateTimeImmutable()));
        if ($model->discount->isPositive()) {
            $tenant->persist(new Discount($partId, $model->discount, new DateTimeImmutable()));
        }
        $tenant->flush();

        $referer = $this->request->query->get('referer');
        if (null !== $referer) {
            $this->setReferer(urldecode($referer).'&part_id='.$entity->toId()->toString());
        }

        $this->event(new PartCreated($entity));

        return $entity;
    }

    protected function createEditDto(Closure $closure): ?object
    {
        /** @var PartView $view */
        $view = $this->registry->getBy(PartView::class, ['id' => $this->request->query->get('id')]);

        $dto = $this->createWithoutConstructor(PartDto::class);
        $dto->partId = $view->toId();
        $dto->manufacturerId = $view->manufacturer->id;
        $dto->name = $view->name;
        $dto->number = $view->number;
        $dto->universal = $view->isUniversal;

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): Part
    {
        $dto = $entity;
        assert($dto instanceof PartDto);

        /** @var Part $entity */
        $entity = $this->registry->getBy(Part::class, $dto->partId);

        $entity->update(
            $dto->name,
            $dto->universal,
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
        $partId = $this->getIdentifier(PartId::class);
        if (!$partId instanceof PartId) {
            throw new BadRequestHttpException('Part required.');
        }

        $dto = $this->createWithoutConstructor(PartCaseDTO::class);
        $dto->partId = $partId;

        $form = $this->createFormBuilder($dto)
            ->add('partId', AutocompleteType::class, [
                'label' => 'Запчасть',
                'class' => Part::class,
                'disabled' => true,
            ])
            ->add('vehicleId', AutocompleteType::class, [
                'label' => 'Модель',
                'class' => Model::class,
                'help' => 'Проивзодитель, Модель, Год, Поколение, Комплектация, Лошадинные силы',
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(PartCase::class);
            $em->persist(new PartCase($dto->partId, $dto->vehicleId));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Добавить кузов',
            'button' => 'Добавить',
            'form' => $form->createView(),
            'entity_fields' => [],
            'entity' => $dto,
        ]);
    }
}
