<?php

declare(strict_types=1);

namespace App\Order\Event;

use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
final class OrderItemPartPriceChanged
{
    public function __construct(public UuidInterface $itemId)
    {
    }
}
