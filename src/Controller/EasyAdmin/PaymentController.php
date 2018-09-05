<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Operand;
use App\Manager\PaymentManager;
use Doctrine\ORM\QueryBuilder;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    protected function createNewEntity(): stdClass
    {
        $obj = new stdClass();
        $obj->id = null;
        $obj->recipient = $this->getEntity(Operand::class);
        $obj->description = null;
        $obj->amount = null;

        return $obj;
    }

    /**
     * @param stdClass $entity
     */
    protected function persistEntity($entity): void
    {
        $this->paymentManager->createPayment($entity->recipient, $entity->description, $entity->amount);
    }

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
