<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Operand;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createPayment(Operand $recipient, string $description, Money $money): Payment
    {
        $em = $this->registry->getEntityManager();

        return $em->transactional(function (EntityManagerInterface $em) use ($recipient, $description, $money) {
            $payment = new Payment(
                $recipient,
                $description,
                $money,
                $this->calcSubtotal($em, $recipient, $money)
            );

            $em->persist($payment);

            return $payment;
        });
    }

    public function balance(Operand $operand): Money
    {
        $em = $this->registry->getEntityManager();

        $amount = $em->createQueryBuilder()
            ->select('SUM(payment.amount)')
            ->from(Payment::class, 'payment')
            ->where('payment.recipient = :recipient')
            ->setParameter('recipient', $operand)
            ->getQuery()->getSingleScalarResult();

        return new Money($amount, new Currency('RUB'));
    }

    private function calcSubtotal(EntityManagerInterface $em, Operand $recipient, Money $money): Money
    {
        /** @var Payment|null $lastPayment */
        $lastPayment = $em->createQueryBuilder()
            ->select('payment')
            ->from(Payment::class, 'payment')
            ->where('payment.recipient = :recipient')
            ->orderBy('payment.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $lastPayment) {
            return $money;
        }

        return $lastPayment->getSubtotal()->add($money);
    }
}
