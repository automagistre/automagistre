<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\Part\Entity\PartId;

/**
 * @psalm-immutable
 */
final class PartDecreased
{
    public function __construct(public PartId $partId)
    {
    }
}
