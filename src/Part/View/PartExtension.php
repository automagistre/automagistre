<?php

declare(strict_types=1);

namespace App\Part\View;

use App\Doctrine\Registry;
use App\Order\Entity\OrderItemPart;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Manager\PartManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use LogicException;
use function number_format;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExtension extends AbstractExtension
{
    public function __construct(
        private Registry $registry,
        private PartManager $partManager,
        private ReservationManager $reservationManager,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('part_reserved_in_item', fn (
                OrderItemPart $part,
            ): int => $this->reservationManager->reserved($part)),
            new TwigFunction('part_reservable', fn (
                PartId $partId,
            ): int => $this->reservationManager->reservable($partId)),
            new TwigFunction('part_by_id', fn (PartId $partId): Part => $this->partManager->byId($partId)),
            new TwigFunction(
                'part_view',
                fn (PartId $partId): PartView => $this->registry->find(PartView::class, $partId) ?? throw new LogicException('Part not found.'),
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
