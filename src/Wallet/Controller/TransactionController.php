<?php

declare(strict_types=1);

namespace App\Wallet\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\WalletId;
use App\Wallet\Entity\WalletTransactionView;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class TransactionController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $walletId = $this->getIdentifierOrNull(WalletId::class);

        if ($walletId instanceof WalletId) {
            $qb->andWhere('entity.walletId = :walletId')
                ->setParameter('walletId', $walletId)
            ;
        }

        $qb
            ->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC')
        ;

        return $qb;
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
        $dqlFilter = null,
    ): QueryBuilder {
        if (!Uuid::isValid($searchQuery)) {
            return parent::createSearchQueryBuilder($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);
        }

        return $this->registry->manager()->createQueryBuilder()
            ->select('t')
            ->from(WalletTransactionView::class, 't')
            ->where('t.sourceId = :sourceId')
            ->setParameter('sourceId', $searchQuery)
            ->orderBy('t.createdAt', 'DESC')
            ->addOrderBy('t.id', 'DESC')
        ;
    }
}
