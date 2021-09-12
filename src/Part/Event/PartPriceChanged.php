<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\MessageBus\Async;
use App\Part\Entity\PartId;

/**
 * @psalm-immutable
 */
final class PartPriceChanged implements Async
{
    public function __construct(public PartId $partId)
    {
    }
}
