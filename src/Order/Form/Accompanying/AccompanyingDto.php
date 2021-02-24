<?php

declare(strict_types=1);

namespace App\Order\Form\Accompanying;

use App\Part\Entity\PartId;
use Money\Money;

/**
 * @psalm-immutable
 */
final class AccompanyingDto
{
    public bool $enabled = false;

    public function __construct(
        public PartId $partId,
        public int $quantity,
        public int $usageCount,
        public Money $price,
    ) {
    }
}
