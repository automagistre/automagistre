<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Operand;
use App\Entity\Wallet;
use App\Manager\PaymentManager;
use Doctrine\ORM\QueryBuilder;
use LogicException;
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
        $recipient = $this->getEntity(Wallet::class);
        if (!$recipient instanceof Wallet) {
            throw new LogicException('Wallet required.');
        }

        $obj = new stdClass();
        $obj->id = null;
        $obj->recipient = $recipient;
        $obj->description = null;
        $obj->amount = null;
        $obj->wallet = null;

        return $obj;
    }

    /**
     * @param stdClass $entity
     */
    protected function persistEntity($entity): void
    {
        $this->em->transactional(function () use ($entity): void {
            /** @var Money $money */
            $money = $entity->amount;
            $description = $entity->description;

            $this->paymentManager->createPayment($entity->recipient, $description, $money);

            if ($entity->wallet instanceof Wallet) {
                $this->paymentManager->createPayment($entity->wallet, $description, $money);
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

        $owner = $this->getEntity(Operand::class);
        if ($owner instanceof Operand) {
            $qb
                ->join('entity.recipient', 'wallet')
                ->andWhere('wallet.owner = :owner')
                ->setParameter('owner', $owner);
        }

        $wallet = $this->getEntity(Wallet::class);
        if ($wallet instanceof Wallet) {
            $qb
                ->andWhere('entity.recipient = :wallet')
                ->setParameter('wallet', $wallet);
        }

        return $qb;
    }
}
