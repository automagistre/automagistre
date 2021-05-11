<?php

declare(strict_types=1);

namespace App\Storage\Event;

use App\MessageBus\Async;
use App\Storage\Entity\InventorizationId;

final class InventorizationClosed implements Async
{
    public function __construct(public InventorizationId $inventorizationId)
    {
    }
}
