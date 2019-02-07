<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Landlord\MC\Equipment;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EquipmentController extends AbstractController
{
    /**
     * @param Equipment $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'show'));
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
        $qb = $this->registry->repository(Equipment::class)->createQueryBuilder('entity');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin('entity.model', 'carModel')
            ->leftJoin('carModel.manufacturer', 'manufacturer');

        foreach (\explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('entity.equipment.engine.name', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModel.localizedName', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('manufacturer.localizedName', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }
}
