<?php

declare(strict_types=1);

namespace App\Enum;

use Premier\Enum\Enum;

/**
 * @method static self unknown()
 * @method string toCode()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarTransmission extends Enum
{
    private const UNKNOWN = 0;
    private const AUTOMATIC = 1;
    private const ROBOT = 2;
    private const VARIATOR = 3;
    private const MECHANICAL = 4;
    private const AUTOMATIC_5 = 5;
    private const AUTOMATIC_7 = 6;

    protected static array $name = [
        self::UNKNOWN => 'Неопределена',
        self::AUTOMATIC => 'Автоматическая',
        self::ROBOT => 'Робот',
        self::VARIATOR => 'Вариатор',
        self::MECHANICAL => 'Механическая',
        self::AUTOMATIC_5 => 'Автоматическая (5 ступеней)',
        self::AUTOMATIC_7 => 'Автоматическая (7 ступеней)',
    ];

    protected static array $code = [
        self::UNKNOWN => '-',
        self::AUTOMATIC => 'AT',
        self::ROBOT => 'AMT',
        self::VARIATOR => 'CVT',
        self::MECHANICAL => 'MT',
        self::AUTOMATIC_5 => 'AT-5',
        self::AUTOMATIC_7 => 'AT-7',
    ];
}
