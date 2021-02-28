<?php

declare(strict_types=1);

namespace App\Order\Form\Related;

use App\Part\Entity\PartView;
use Money\Money;

/**
 * @psalm-immutable
 */
final class RelatedDto
{
    public bool $enabled = false;

    public function __construct(
        public PartView $part,
        public int $quantity,
        public int $usageCount,
        public Money $price,
    ) {
    }
}
