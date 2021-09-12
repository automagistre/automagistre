<?php

declare(strict_types=1);

namespace App\Income\Event;

use App\Income\Entity\IncomeId;
use App\MessageBus\Async;

/**
 * @psalm-immutable
 */
final class IncomeAccrued implements Async
{
    public function __construct(public IncomeId $incomeId)
    {
    }
}
