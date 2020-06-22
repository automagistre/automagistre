<?php

declare(strict_types=1);

namespace App\Wallet\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\WalletId;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
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
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $walletId = $this->getIdentifier(WalletId::class);
        if ($walletId instanceof WalletId) {
            $qb->andWhere('entity.walletId = :walletId')
                ->setParameter('walletId', $walletId);
        }

        $qb
            ->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC');

        return $qb;
    }
}
