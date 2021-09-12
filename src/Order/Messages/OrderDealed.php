<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\MessageBus\Async;
use App\Order\Entity\OrderId;

/**
 * @psalm-immutable
 */
final class OrderDealed implements Async
{
    public function __construct(public OrderId $orderId)
    {
    }
}
