<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Tenant\OrderItemPart;
use App\Manager\PartManager;
use App\Manager\ReservationManager;
use App\Part\Domain\Part;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExtension extends AbstractExtension
{
    private PartManager $partManager;

    private ReservationManager $reservationManager;

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
            new TwigFunction('part_in_stock', fn (Part $part): int => $this->partManager->inStock($part)),
            new TwigFunction('part_crosses', fn (Part $part): array => $this->partManager->getCrosses($part)),
            new TwigFunction('part_crosses_in_stock', fn (Part $part): array => $this->partManager->crossesInStock($part)),
            new TwigFunction('part_reserved', fn (Part $part): int => $this->reservationManager->reserved($part)),
            new TwigFunction('part_reserved_in_item', fn (OrderItemPart $part): int => $this->reservationManager->reserved($part)),
            new TwigFunction('part_reservable', fn (Part $part): int => $this->reservationManager->reservable($part)),
            new TwigFunction('part_suggest_price', fn (Part $part): Money => $this->partManager->suggestPrice($part)),
        ];
    }
}
