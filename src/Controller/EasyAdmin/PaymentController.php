<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Operand;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentController extends AbstractController
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

        $recipient = $this->getEntity(Operand::class);
        if ($recipient instanceof Operand) {
            $qb->andWhere('entity.recipient = :recipient')
                ->setParameter('recipient', $recipient);
        }

        return $qb;
    }
}
