<?php

declare(strict_types=1);

namespace App\Wallet\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\Wallet;
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

        $recipient = $this->getEntity(Wallet::class);
        if ($recipient instanceof Wallet) {
            $qb->andWhere('entity.wallet = :recipient')
                ->setParameter('recipient', $recipient);
        }

        $qb
            ->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC');

        return $qb;
    }
}
