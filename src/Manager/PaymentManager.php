<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Transaction;
use App\Entity\Transactional;
use App\Events;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(RegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    public function createPayment(Transactional $recipient, string $description, Money $money): Transaction
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManager('tenant');

        $payment = $em->transactional(function (EntityManagerInterface $em) use ($recipient, $description, $money) {
            $transactionClass = $recipient->getTransactionClass();

            $payment = new $transactionClass(
                $recipient,
                $description,
                $money,
                $this->calcSubtotal($em, $recipient, $money)
            );

            $em->persist($payment);

            return $payment;
        });

        $this->dispatcher->dispatch(Events::PAYMENT_CREATED, new GenericEvent($payment));

        return $payment;
    }

    public function balance(Transactional $transactional): Money
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManager('tenant');

        $qb = $em->createQueryBuilder()
            ->select('SUM(payment.amount.amount)')
            ->from($transactional->getTransactionClass(), 'payment');

        if ($transactional instanceof Operand) {
            $qb
                ->where('payment.recipient.uuid = :recipient')
                ->setParameter('recipient', $transactional->uuid(), 'uuid_binary');
        } else {
            $qb
                ->where('payment.recipient = :recipient')
                ->setParameter('recipient', $transactional);
        }

        $amount = $qb->getQuery()->getSingleScalarResult();

        return new Money($amount, new Currency('RUB'));
    }

    private function calcSubtotal(EntityManagerInterface $em, Transactional $transactional, Money $money): Money
    {
        /** @var Transaction|null $lastPayment */
        $qb = $em->createQueryBuilder()
            ->select('payment')
            ->from($transactional->getTransactionClass(), 'payment')
            ->orderBy('payment.id', 'DESC')
            ->setMaxResults(1);

        if ($transactional instanceof Operand) {
            $qb
                ->where('payment.recipient.uuid = :recipient')
                ->setParameter('recipient', $transactional->uuid(), 'uuid_binary');
        } else {
            $qb
                ->where('payment.recipient = :recipient')
                ->setParameter('recipient', $transactional);
        }

        $lastPayment = $qb
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $lastPayment) {
            return $money;
        }

        return $lastPayment->getSubtotal()->add($money);
    }
}
