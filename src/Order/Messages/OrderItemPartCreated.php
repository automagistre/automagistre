<?php

declare(strict_types=1);

namespace App\Order\Messages;

use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
final class OrderItemPartCreated
{
    public function __construct(public UuidInterface $id)
    {
    }
}
