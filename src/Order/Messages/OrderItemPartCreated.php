<?php

declare(strict_types=1);

namespace App\Order\Messages;

use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
final class OrderItemPartCreated
{
    public UuidInterface $id;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }
}
