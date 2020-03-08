<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Doctrine\Registry;
use App\Entity\Landlord\MC\Equipment;
use function assert;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function strtolower;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EquipmentController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Equipment);

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
        $registry = $this->container->get(Registry::class);

        $qb = $registry->repository(Equipment::class)->createQueryBuilder('entity');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin('entity.model', 'carModel')
            ->leftJoin('carModel.manufacturer', 'manufacturer');

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

            $qb->setParameter($key, '%'.strtolower($searchString).'%');
        }

        return $qb;
    }
}
