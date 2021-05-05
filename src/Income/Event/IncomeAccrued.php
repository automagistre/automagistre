<?php

declare(strict_types=1);

namespace App\Income\Event;

use App\Income\Entity\IncomeId;

/**
 * @psalm-immutable
 */
final class IncomeAccrued
{
    public function __construct(public IncomeId $incomeId)
    {
    }
}
