<?php

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getCode()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarTransmission extends Enum
{
    const AUTOMATIC = 1;
    const ROBOT = 2;
    const VARIATOR = 3;
    const MECHANICAL = 4;

    protected static $name = [
        self::AUTOMATIC  => 'Автоматическая',
        self::ROBOT      => 'Робот',
        self::VARIATOR   => 'Вариатор',
        self::MECHANICAL => 'Механическая',
    ];

    protected static $code = [
        self::AUTOMATIC  => 'AT',
        self::ROBOT      => 'AMT',
        self::VARIATOR   => 'CVT',
        self::MECHANICAL => 'MT',
    ];
}
