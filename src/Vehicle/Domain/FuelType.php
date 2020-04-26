<?php

declare(strict_types=1);

namespace App\Vehicle\Domain;

use Premier\Enum\Enum;

/**
 * @method static self unknown()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FuelType extends Enum
{
    private const UNKNOWN = 0;
    private const PETROL = 1;
    private const DIESEL = 2;
    private const ETHANOL = 3;
    private const ELECTRIC = 4;

    protected static array $name = [
        self::UNKNOWN => 'Неопределён',
        self::PETROL => 'Бензин',
        self::DIESEL => 'Дизель',
        self::ETHANOL => 'Этанол',
        self::ELECTRIC => 'Электрический',
    ];
}
