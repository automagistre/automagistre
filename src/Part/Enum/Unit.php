<?php

declare(strict_types=1);

namespace App\Part\Enum;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self thing()
 * @method static self liter()
 * @method static self package()
 * @method static self kilogram()
 * @method static self meter()
 * @method static self milliliter()
 * @method string toLabel()
 * @method string toShortLabel()
 */
final class Unit extends Enum
{
    private const THING = 1;
    private const PACKAGE = 2;
    private const MILLILITER = 3;
    private const LITER = 4;
    private const GRAM = 5;
    private const KILOGRAM = 6;
    private const MILLIMETER = 7;
    private const METER = 8;

    private static array $shortLabel = [
        self::THING => 'шт',
        self::PACKAGE => 'упак',
        self::MILLILITER => 'мл',
        self::LITER => 'л',
        self::GRAM => 'гр',
        self::KILOGRAM => 'кг',
        self::MILLIMETER => 'мм',
        self::METER => 'м',
    ];

    private static array $label = [
        self::THING => 'Штука',
        self::PACKAGE => 'Упаковка',
        self::MILLILITER => 'Миллилитр',
        self::LITER => 'Литр',
        self::GRAM => 'Грамм',
        self::KILOGRAM => 'Килограмм',
        self::MILLIMETER => 'Миллиметр',
        self::METER => 'Метр',
    ];
}
