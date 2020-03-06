<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\OperandTransaction;
use App\Entity\Tenant\Transaction;
use App\Event\PaymentCreated;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    private Registry $registry;

    private EventDispatcherInterface $dispatcher;

    public function __construct(Registry $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    public function createPayment(Operand $recipient, string $description, Money $money): Transaction
    {
        $em = $this->registry->manager(Operand::class);

        $payment = $em->transactional(static function (EntityManagerInterface $em) use (
            $recipient,
            $description,
            $money
        ): OperandTransaction {
            $payment = new OperandTransaction(
                $recipient,
                $description,
                $money,
            );

            $em->persist($payment);

            return $payment;
        });

        $this->dispatcher->dispatch(new PaymentCreated($payment));

        return $payment;
    }

    public function balance(Operand $transactional): Money
    {
        $em = $this->registry->manager(OperandTransaction::class);

        $qb = $em->createQueryBuilder()
            ->select('SUM(CAST(payment.amount.amount AS int))')
            ->from(OperandTransaction::class, 'payment')
            ->where('payment.recipient.id = :recipient')
            ->setParameter('recipient', $transactional->getId());

        $amount = $qb->getQuery()->getSingleScalarResult();

        return new Money($amount, new Currency('RUB'));
    }
}
