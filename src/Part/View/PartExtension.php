<?php

declare(strict_types=1);

namespace App\Part\View;

use App\Order\Entity\OrderItemPart;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Manager\PartManager;
use App\Storage\Manager\ReservationManager;
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
            new TwigFunction('part_in_stock', fn (Part $part): int => $this->partManager->inStock($part->toId())),
            new TwigFunction('part_crosses', fn (Part $part): array => $this->partManager->getCrosses($part->toId())),
            new TwigFunction('part_crosses_in_stock', fn (Part $part
            ): array => $this->partManager->crossesInStock($part->toId())),
            new TwigFunction('part_reserved', fn (Part $part
            ): int => $this->reservationManager->reserved($part->toId())),
            new TwigFunction('part_reserved_in_item', fn (OrderItemPart $part
            ): int => $this->reservationManager->reserved($part)),
            new TwigFunction('part_reservable', fn (PartId $partId
            ): int => $this->reservationManager->reservable($partId)),
            new TwigFunction('part_suggest_price', fn (PartId $partId
            ): Money => $this->partManager->suggestPrice($partId)),
            new TwigFunction('part_price', fn (PartId $partId): Money => $this->partManager->price($partId)),
            new TwigFunction('part_by_id', fn (PartId $partId): Part => $this->partManager->byId($partId)),
        ];
    }
}
