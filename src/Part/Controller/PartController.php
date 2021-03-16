<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\Discount;
use App\Part\Entity\Part;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Part\Entity\PartView;
use App\Part\Entity\Price;
use App\Part\Form\PartDto;
use App\Part\Form\PartType;
use App\Part\Manager\PartManager;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Closure;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function array_diff;
use function array_keys;
use function array_map;
use function array_unique;
use function array_values;
use function assert;
use function count;
use function explode;
use function mb_strtoupper;
use function sprintf;
use function str_replace;
use function strpos;
use function strtoupper;
use function trim;
use function urldecode;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartController extends AbstractController
{
    private PartManager $partManager;

    private ReservationManager $reservationManager;

    public function __construct(PartManager $partManager, ReservationManager $reservationManager)
    {
        $this->partManager = $partManager;
        $this->reservationManager = $reservationManager;
    }

    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = new PartDto();

        $form = $this->createForm(PartType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = PartId::generate();

            $em->persist(
                new Part(
                    $id,
                    $dto->manufacturerId,
                    $dto->name,
                    new PartNumber($dto->number),
                    $dto->universal,
                    $dto->unit,
                ),
            );

            if (!$dto->price->isZero()) {
                $em->persist(
                    new Price(
                        $id,
                        $dto->price,
                    )
                );
            }

            if (!$dto->discount->isZero()) {
                $em->persist(
                    new Discount(
                        $id,
                        $dto->discount,
                    )
                );
            }

            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id).' | '.$this->formatMoney($dto->price),
            ]);
        }

        if (null !== $dto->manufacturerId && null !== $dto->number && $form->isSubmitted()) {
            /** @var null|PartView $part */
            $part = $em->createQueryBuilder()
                ->select('t')
                ->from(PartView::class, 't')
                ->where('t.manufacturer.id = :manufacturerId')
                ->andWhere('t.number = :number')
                ->getQuery()
                ->setParameter('manufacturerId', $dto->manufacturerId)
                ->setParameter('number', $dto->number)
                ->getOneOrNullResult()
            ;

            if (null !== $part) {
                return new JsonResponse([
                    'id' => $part->toId()->toString(),
                    'text' => $this->display($part->toId()).' | '.$this->formatMoney($part->price),
                ]);
            }
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'part',
            'label' => 'Новая запчасть',
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

            $parameters['orders'] = $this->partManager->inOrders($part->toId());
            $parameters['reservedIn'] = array_map(
                fn (Order $order): string => $order->toId()->toString(),
                $this->reservationManager->orders($part->toId())
            );
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
                ->join(PartCase::class, 'partCase', Join::WITH, 'carModel.id = partCase.vehicleId')
                ->where('partCase.partId = :part')
                ->setParameter('part', $part->toId())
                ->getQuery()
                ->getResult(AbstractQuery::HYDRATE_ARRAY)
            ;
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
            ->orderBy('part.'.$sortField, $sortDirection)
        ;

        $vehicleId = $this->getIdentifier(VehicleId::class);

        if (!$isPlusExist && $vehicleId instanceof VehicleId) {
            $carModel = $this->registry->getBy(Model::class, ['id' => $vehicleId]);

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
                        'case' => '%'.strtoupper($carModel->caseName).'%',
                        'universal' => true,
                    ])
                ;
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
                ->setParameter($key, '%'.mb_strtoupper(trim($searchString)).'%')
            ;
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
            ->orderBy('part.quantity', 'DESC')
        ;

        $paginator = new Pagerfanta(new QueryAdapter($qb, false, false));
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($query->getInt('page', 1));

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

            if ($analog && !$part->hasKeepingStock()) {
                $text = '[ПРОДАТЬ] '.$text;
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
        $currentPageResults = (array) $paginator->getCurrentPageResults();

        if (3 >= count($currentPageResults)) {
            foreach ($currentPageResults as $part) {
                // @var $part PartView
                $data[$part->toId()->toString()] = $normalizer($part);

                $analogs = [...$analogs, ...$part->analogs];
            }
        } else {
            $data = array_map($normalizer, $currentPageResults);
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
                ->getResult()
            ;

            foreach ($analogs as $analog) {
                $data[] = $normalizer($analog, true);
            }
        }

        return $this->json(['results' => array_values($data)]);
    }

    protected function createNewEntity(): PartDto
    {
        return new PartDto();
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Part
    {
        $dto = $entity;
        assert($dto instanceof PartDto);

        $partId = PartId::generate();
        $entity = new Part(
            $partId,
            $dto->manufacturerId,
            $dto->name,
            new PartNumber($dto->number),
            $dto->universal,
            $dto->unit,
        );

        parent::persistEntity($entity);

        $tenant = $this->registry->manager(Price::class);
        $tenant->persist(new Price($partId, $dto->price, new DateTimeImmutable()));

        if ($dto->discount->isPositive()) {
            $tenant->persist(new Discount($partId, $dto->discount, new DateTimeImmutable()));
        }
        $tenant->flush();

        $referer = $this->request->query->get('referer');

        if (null !== $referer) {
            $this->setReferer(urldecode($referer).'&part_id='.$entity->toId()->toString());
        }

        return $entity;
    }

    protected function createEditDto(Closure $closure): ?object
    {
        /** @var PartView $view */
        $view = $this->registry->getBy(PartView::class, ['id' => $this->request->query->get('id')]);

        $dto = new PartDto();
        $dto->partId = $view->toId();
        $dto->manufacturerId = $view->manufacturer->id;
        $dto->name = $view->name;
        $dto->number = (string) $view->number;
        $dto->universal = $view->isUniversal;
        $dto->unit = $view->unit;

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
            $dto->unit,
        );

        parent::updateEntity($entity);

        if ($dto->universal) {
            $this->registry->repository(PartCase::class)
                ->createQueryBuilder('entity')
                ->delete()
                ->where('entity.partId = :part')
                ->setParameter('part', $entity->toId())
                ->getQuery()
                ->execute()
            ;
        }

        return $entity;
    }
}
