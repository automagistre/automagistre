<?php

declare(strict_types=1);

namespace App\Storage\Enum;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self manual()
 * @method static self income()
 * @method static self order()
 * @method static self inventorization()
 * @method bool   isManual()
 * @method bool   isIncome()
 * @method bool   isOrder()
 * @method bool   isInventorization()
 */
final class MotionType extends Enum
{
    private const MANUAL = 1;
    private const INCOME = 2;
    private const ORDER = 3;
    private const INVENTORIZATION = 4;
}
