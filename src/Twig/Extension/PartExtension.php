<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Landlord\Part;
use App\Manager\PartManager;
use App\Manager\ReservationManager;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExtension extends AbstractExtension
{
    /**
     * @var PartManager
     */
    private $partManager;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(PartManager $partManager, ReservationManager $reservationManager)
    {
        $this->partManager = $partManager;
        $this->reservationManager = $reservationManager;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('part_in_stock', function (Part $part): int {
                return $this->partManager->inStock($part);
            }),
            new TwigFunction('part_crosses', function (Part $part): array {
                return $this->partManager->getCrosses($part);
            }),
            new TwigFunction('part_crosses_in_stock', function (Part $part): array {
                return $this->partManager->crossesInStock($part);
            }),
            new TwigFunction('part_reserved', function (Part $part): int {
                return $this->reservationManager->reserved($part);
            }),
            new TwigFunction('part_reservable', function (Part $part): int {
                return $this->reservationManager->reservable($part);
            }),
            new TwigFunction('part_suggest_price', function (Part $part): Money {
                return $this->partManager->suggestPrice($part);
            }),
        ];
    }
}
