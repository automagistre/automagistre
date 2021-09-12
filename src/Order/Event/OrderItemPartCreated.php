<?php

declare(strict_types=1);

namespace App\Order\Event;

use App\MessageBus\Async;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
final class OrderItemPartCreated implements Async
{
    public function __construct(public UuidInterface $itemId)
    {
    }
}
