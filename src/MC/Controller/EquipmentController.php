<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use App\Vehicle\Entity\Model;
use function array_map;
use function assert;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use function str_replace;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class EquipmentController extends AbstractController
{
    protected function createNewEntity(): McEquipment
    {
        return new McEquipment(
            McEquipmentId::generate(),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof McEquipment);

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('McEquipment', 'show', ['id' => $entity->toId()->toString()]));
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = 'id',
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->registry->repository(McEquipment::class)->createQueryBuilder('entity');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin(Model::class, 'carModel', Join::WITH, 'entity.vehicleId = carModel.id')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'carModel.manufacturerId = manufacturer.id');

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(entity.equipment.engine.name)', $key),
                $qb->expr()->like('LOWER(carModel.name)', $key),
                $qb->expr()->like('LOWER(carModel.caseName)', $key),
                $qb->expr()->like('LOWER(carModel.localizedName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower($searchString).'%');
        }

        $qb->orderBy('entity.'.$sortField, $sortDirection);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;

        $queryString = str_replace(['.', ',', '-', '_'], '', (string) $request->query->get('query'));
        $qb = $this->createSearchQueryBuilder((string) $request->query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1));

        return $this->json([
            'results' => array_map(
                fn (McEquipment $equipment) => [
                    'id' => $equipment->toId()->toString(),
                    'text' => $this->display($equipment->toId()),
                ],
                (array) $paginator->getCurrentPageResults()
            ),
        ]);
    }
}
