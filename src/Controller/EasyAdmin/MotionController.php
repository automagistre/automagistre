<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MotionController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $request = $this->request;

        $filter = $request->query->get('filter');

        if ('income' === $filter) {
            $qb->andWhere('entity.quantity >= 0');
        } elseif ('outcome' === $filter) {
            $qb->andWhere('entity.quantity <= 0');
        }

        return $qb;
    }
}
