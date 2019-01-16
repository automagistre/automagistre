<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\IncomePart;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PriceManager
{
    private const MARKUP = 1.15;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function suggestForPart(Part $part): Money
    {
        $em = $this->registry->manager(IncomePart::class);
        $suggestPrice = $part->getPrice();

        $incomePart = $em->createQueryBuilder()
            ->select('entity')
            ->from(IncomePart::class, 'entity')
            ->where('entity.part.id = :part')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameter('part', $part->getId())
            ->getOneOrNullResult();

        if ($incomePart instanceof IncomePart) {
            $incomePriceWithMarkup = $this->markup($incomePart->getPrice());

            if ($incomePriceWithMarkup->greaterThan($suggestPrice)) {
                $suggestPrice = $incomePriceWithMarkup;
            }
        }

        return $suggestPrice;
    }

    private function markup(Money $price): Money
    {
        return $price->multiply(self::MARKUP);
    }
}
