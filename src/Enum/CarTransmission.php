<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getCode()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarTransmission extends Enum
{
    private const AUTOMATIC = 1;
    private const ROBOT = 2;
    private const VARIATOR = 3;
    private const MECHANICAL = 4;

    protected static $name = [
        self::AUTOMATIC => 'Автоматическая',
        self::ROBOT => 'Робот',
        self::VARIATOR => 'Вариатор',
        self::MECHANICAL => 'Механическая',
    ];

    protected static $code = [
        self::AUTOMATIC => 'AT',
        self::ROBOT => 'AMT',
        self::VARIATOR => 'CVT',
        self::MECHANICAL => 'MT',
    ];
}
