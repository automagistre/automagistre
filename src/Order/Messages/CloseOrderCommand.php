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
    public function __construct(public OrderId $orderId, public OrderSatisfaction $satisfaction)
    {
    }
}
