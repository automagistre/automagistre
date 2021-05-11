<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\Part\Entity\PartId;

/**
 * @psalm-immutable
 */
final class PartAccrued
{
    public function __construct(public PartId $partId)
    {
    }
}
