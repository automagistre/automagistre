<?php

declare(strict_types=1);

namespace App\Storage\Enum;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self old()
 * @method static self manual()
 * @method static self income()
 * @method static self order()
 */
final class Source extends Enum
{
    private const OLD = 1;
    private const MANUAL = 1;
    private const INCOME = 1;
    private const ORDER = 1;
}
