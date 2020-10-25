<?php

declare(strict_types=1);

namespace App\Order\Enum;

use Premier\Enum\Enum;

/**
 * @method bool   isGood()
 * @method string toDisplayName()
 */
final class OrderSatisfaction extends Enum
{
    private const OLD = 0;
    private const GOOD = 1;
    private const BAD = 2;
    private const UNKNOWN = 3;

    protected static array $displayName = [
        self::OLD => 'Не известно',
        self::GOOD => 'Да',
        self::BAD => 'Нет',
        self::UNKNOWN => 'Сложность сказать',
    ];

    /**
     * @return self[]
     */
    public static function selectable(): array
    {
        return self::all([self::create(self::OLD)], true);
    }
}
