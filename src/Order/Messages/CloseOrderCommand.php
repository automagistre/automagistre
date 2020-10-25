<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Order\Entity\OrderId;
use App\Order\Enum\OrderSatisfaction;

/**
 * @psalm-immutable
 */
final class CloseOrderCommand
{
    public OrderId $orderId;

    public OrderSatisfaction $satisfaction;

    public function __construct(OrderId $orderId, OrderSatisfaction $satisfaction)
    {
        $this->orderId = $orderId;
        $this->satisfaction = $satisfaction;
    }
}
