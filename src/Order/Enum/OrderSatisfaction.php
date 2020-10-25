<?php

declare(strict_types=1);

namespace App\Order\Enum;

use Premier\Enum\Enum;

/**
 * @method string toDisplayName()
 */
final class OrderSatisfaction extends Enum
{
    private const GOOD = 1;
    private const BAD = 2;
    private const UNKNOWN = 3;

    protected static array $displayName = [
        self::GOOD => 'Да',
        self::BAD => 'Нет',
        self::UNKNOWN => 'Сложность сказать',
    ];
}
