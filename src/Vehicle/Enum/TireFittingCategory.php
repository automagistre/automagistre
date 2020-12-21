<?php

declare(strict_types=1);

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

/**
 * @method string toDisplayName()
 * @method static self unknown()
 * @method static self suv()
 *
 * @psalm-immutable
 */
final class TireFittingCategory extends Enum
{
    private const UNKNOWN = 0;
    private const CAR = 1;
    private const SUV = 2;
    private const CROSSOVER = 3;
    private const MINIVAN = 4;

    protected static array $name = [
        self::UNKNOWN => 'unknown',
        self::CAR => 'car',
        self::SUV => 'suv',
        self::CROSSOVER => 'crossover',
        self::MINIVAN => 'minivan',
    ];

    protected static array $displayName = [
        self::UNKNOWN => 'Неопределён',
        self::CAR => 'Легковая',
        self::SUV => 'Внедорожник',
        self::CROSSOVER => 'Кроссовер',
        self::MINIVAN => 'Минивен',
    ];
}
