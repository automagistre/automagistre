<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Order\Entity\OrderId;

/**
 * @psalm-immutable
 */
final class OrderDealed
{
    public OrderId $orderId;

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }
}
