<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Costil;
use App\Entity\Operand;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Money\Money;
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
        $obj->useCassa = true;

        return $obj;
    }

    /**
     * @param stdClass $entity
     */
    protected function persistEntity($entity): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) use ($entity): void {
            /** @var Money $money */
            $money = $entity->amount;
            $description = $entity->description;

            $this->paymentManager->createPayment($entity->recipient, $description, $money);

            if ($entity->useCassa) {
                /** @var Operand $cassa */
                $cassa = $em->getReference(Operand::class, Costil::CASHBOX);
                $this->paymentManager->createPayment($cassa, $description, $money);
            }
        });
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
