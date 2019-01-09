<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Landlord\Part;
use App\Entity\Tenant\IncomePart;
use App\Entity\Tenant\OrderItemPart;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PriceManager
{
    private const MARKUP = 1.15;

    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function suggestForPart(Part $part): Money
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(IncomePart::class);
        $suggestPrice = $part->getPrice();

        $incomePart = $em->createQueryBuilder()
            ->select('entity')
            ->from(IncomePart::class, 'entity')
            ->where('entity.part.uuid = :part')
            ->orderBy('entity.price.amount', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameter('part', $part->uuid(), 'uuid_binary')
            ->getOneOrNullResult();

        if ($incomePart instanceof IncomePart) {
            $incomePriceWithMarkup = $this->markup($incomePart->getPrice());

            if ($incomePriceWithMarkup->greaterThan($suggestPrice)) {
                $suggestPrice = $incomePriceWithMarkup;
            }
        }

        $lastOrderItemPart = $em->createQueryBuilder()
            ->select('entity')
            ->from(OrderItemPart::class, 'entity')
            ->where('entity.part.uuid = :part')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameter('part', $part->uuid(), 'uuid_binary')
            ->getOneOrNullResult();

        if ($lastOrderItemPart instanceof OrderItemPart) {
            $lastPrice = $lastOrderItemPart->getPrice();

            if ($lastPrice->greaterThan($suggestPrice)) {
                $suggestPrice = $lastPrice;
            }
        }

        return $suggestPrice;
    }

    private function markup(Money $price): Money
    {
        return $price->multiply(self::MARKUP);
    }
}
