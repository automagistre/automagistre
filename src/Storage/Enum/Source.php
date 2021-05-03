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
 * @method static self inventory()
 * @method bool   isManual()
 * @method bool   isIncome()
 * @method bool   isOrder()
 * @method bool   isInventory()
 */
final class Source extends Enum
{
    private const OLD = 0;
    private const MANUAL = 1;
    private const INCOME = 2;
    private const ORDER = 3;
    private const INVENTORY = 4;
}
