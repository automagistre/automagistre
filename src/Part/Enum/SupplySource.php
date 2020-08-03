<?php

declare(strict_types=1);

namespace App\Part\Enum;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self manual()
 * @method static self income()
 * @method bool   isManual()
 * @method bool   isIncome()
 */
final class SupplySource extends Enum
{
    private const MANUAL = 1;
    private const INCOME = 2;
}
