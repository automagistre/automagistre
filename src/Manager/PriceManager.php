<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\IncomePart;
use App\Entity\OrderItemPart;
use App\Entity\Part;
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
        $em = $this->registry->getEntityManager();
        $suggestPrice = $part->getPrice();

        $incomePart = $em->createQueryBuilder()
            ->select('entity')
            ->from(IncomePart::class, 'entity')
            ->where('entity.part = :part')
            ->orderBy('entity.price.amount', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameters([
                'part' => $part,
            ])
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
            ->where('entity.part = :part')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameters([
                'part' => $part,
            ])
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
