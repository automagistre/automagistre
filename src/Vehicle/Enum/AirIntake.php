<?php

declare(strict_types=1);

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

/**
 * @method string toCode()
 * @method static self unknown()
 * @method static self fromCode()
 */
final class AirIntake extends Enum
{
    private const UNKNOWN = 0;
    private const ATMOSPHERIC = 1;
    private const TURBO = 2;

    protected static array $name = [
        self::UNKNOWN => 'Неопределён',
        self::ATMOSPHERIC => 'Атмосферный',
        self::TURBO => 'Турбированный',
    ];

    protected static array $code = [
        self::UNKNOWN => 'unknown',
        self::ATMOSPHERIC => 'atmo',
        self::TURBO => 'turbo',
    ];
}
