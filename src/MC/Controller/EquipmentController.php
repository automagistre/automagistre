<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Domain\Manufacturer;
use App\MC\Entity\McEquipment;
use function assert;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;

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
        assert($entity instanceof McEquipment);

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
        $qb = $this->registry->repository(McEquipment::class)->createQueryBuilder('entity');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin('entity.model', 'carModel')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'carModel.manufacturerId = manufacturer.uuid');

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

        return $qb;
    }
}
