<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\Part\Entity\PartId;
use App\MessageBus\Async;

/**
 * @psalm-immutable
 */
final class PartAccrued implements Async
{
    public function __construct(public PartId $partId)
    {
    }
}
