<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Tenant\Wallet;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletTransactionController extends AbstractController
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

        $recipient = $this->getEntity(Wallet::class);
        if ($recipient instanceof Wallet) {
            $qb->andWhere('entity.recipient = :recipient')
                ->setParameter('recipient', $recipient);
        }

        $qb->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC');

        return $qb;
    }
}
