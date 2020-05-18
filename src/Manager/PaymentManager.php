<?php

declare(strict_types=1);

namespace App\Manager;

use App\Customer\Domain\Operand;
use App\Entity\Tenant\Transaction;
use App\Entity\Transactional;
use App\Event\PaymentCreated;
use App\Shared\Doctrine\Registry;
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

    public function createPayment(Transactional $recipient, string $description, Money $money): Transaction
    {
        $em = $this->registry->manager($recipient->getTransactionClass());

        $payment = $em->transactional(function (EntityManagerInterface $em) use ($recipient, $description, $money) {
            $transactionClass = $recipient->getTransactionClass();

            $payment = new $transactionClass(
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

    public function balance(Transactional $transactional): Money
    {
        $em = $this->registry->manager($transactional->getTransactionClass());

        $qb = $em->createQueryBuilder()
            ->select('SUM(CAST(payment.amount.amount AS int))')
            ->from($transactional->getTransactionClass(), 'payment');

        if ($transactional instanceof Operand) {
            $qb
                ->where('payment.recipient.id = :recipient')
                ->setParameter('recipient', $transactional->getId());
        } else {
            $qb
                ->where('payment.recipient = :recipient')
                ->setParameter('recipient', $transactional);
        }

        $amount = $qb->getQuery()->getSingleScalarResult();

        return new Money($amount, new Currency('RUB'));
    }
}
