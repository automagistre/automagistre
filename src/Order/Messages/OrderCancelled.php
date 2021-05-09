<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Order\Entity\OrderId;

/**
 * @psalm-immutable
 */
final class OrderCancelled
{
    public function __construct(public OrderId $orderId)
    {
    }
}
