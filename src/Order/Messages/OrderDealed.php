<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Order\Entity\OrderId;

/**
 * @psalm-immutable
 */
final class OrderDealed
{
    public function __construct(public OrderId $orderId)
    {
    }
}
