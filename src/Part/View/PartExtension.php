<?php

declare(strict_types=1);

namespace App\Part\View;

use App\Order\Entity\OrderItemPart;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Manager\PartManager;
use App\Shared\Doctrine\Registry;
use function number_format;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExtension extends AbstractExtension
{
    private Registry $registry;

    private PartManager $partManager;

    private ReservationManager $reservationManager;

    public function __construct(Registry $registry, PartManager $partManager, ReservationManager $reservationManager)
    {
        $this->registry = $registry;
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
            new TwigFunction('part_by_id', fn (PartId $partId): Part => $this->partManager->byId($partId)),
            new TwigFunction(
                'part_view',
                fn (PartId $partId): PartView => $this->registry->getBy(PartView::class, ['id' => $partId]),
            ),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'format_quantity',
                /** @psalm-suppress MissingClosureParamType */
                static function ($value, bool $keepZero = false): string {
                    $formatted = number_format($value / 100, 2);

                    return !$keepZero && '0.00' === $formatted ? '' : $formatted;
                },
            ),
        ];
    }
}
