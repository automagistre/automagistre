<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Transaction;
use App\Entity\Transactional;
use App\Events;
use App\State;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var State
     */
    private $state;

    public function __construct(Registry $registry, EventDispatcherInterface $dispatcher, State $state)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
        $this->state = $state;
    }

    public function createPayment(Transactional $recipient, string $description, Money $money): Transaction
    {
        $em = $this->registry->manager($recipient->getTransactionClass());

        $payment = $em->transactional(function (EntityManagerInterface $em) use ($recipient, $description, $money) {
            $transactionClass = $recipient->getTransactionClass();

            \assert(\class_exists($transactionClass));

            $payment = new $transactionClass(
                $recipient,
                $description,
                $money,
                $this->calcSubtotal($em, $recipient, $money),
                $this->state->user()
            );

            $em->persist($payment);

            return $payment;
        });

        $this->dispatcher->dispatch(Events::PAYMENT_CREATED, new GenericEvent($payment));

        return $payment;
    }

    public function balance(Transactional $transactional): Money
    {
        $em = $this->registry->manager($transactional->getTransactionClass());

        $qb = $em->createQueryBuilder()
            ->select('SUM(payment.amount.amount)')
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
                ->where('payment.recipient.id = :recipient')
                ->setParameter('recipient', $transactional->getId());
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
