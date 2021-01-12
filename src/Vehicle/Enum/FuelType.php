<?php

declare(strict_types=1);

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

/**
 * @method string toCode()
 * @method static self fromCode()
 * @method static self unknown()
 */
final class FuelType extends Enum
{
    private const UNKNOWN = 0;
    private const PETROL = 1;
    private const DIESEL = 2;
    private const ETHANOL = 3;
    private const ELECTRIC = 4;
    private const HYBRID = 5;

    protected static array $name = [
        self::UNKNOWN => 'Неопределён',
        self::PETROL => 'Бензин',
        self::DIESEL => 'Дизель',
        self::ETHANOL => 'Этанол',
        self::ELECTRIC => 'Электрический',
        self::HYBRID => 'Гибрид',
    ];

    protected static array $code = [
        self::UNKNOWN => 'unknown',
        self::PETROL => 'petrol',
        self::DIESEL => 'diesel',
        self::ETHANOL => 'ethanol',
        self::ELECTRIC => 'electric',
        self::HYBRID => 'hybrid',
    ];
}
