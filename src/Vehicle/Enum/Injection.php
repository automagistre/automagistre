<?php

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

/**
 * @method string toCode()
 * @method static self unknown()
 * @method static self fromCode()
 */
final class Injection extends Enum
{
    private const UNKNOWN = 0;
    private const CLASSIC = 1;
    private const DIRECT = 2;

    protected static array $name = [
        self::UNKNOWN => 'Неопределён',
        self::CLASSIC => 'Классический',
        self::DIRECT => 'Непосредственный впрыск',
    ];

    protected static array $code = [
        self::UNKNOWN => 'unknown',
        self::CLASSIC => 'classic',
        self::DIRECT => 'direct',
    ];
}
