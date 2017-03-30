<?php

namespace AppBundle\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getCode()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarWheelDrive extends Enum
{
    const FRONT_WHEEL_DRIVE = 1;
    const REAR_WHEEL_DRIVE = 2;
    const ALL_WHEEL_DRIVE = 3;

    protected static $name = [
        self::FRONT_WHEEL_DRIVE => 'Передний',
        self::REAR_WHEEL_DRIVE  => 'Задний',
        self::ALL_WHEEL_DRIVE   => 'Полный',
    ];

    protected static $code = [
        self::FRONT_WHEEL_DRIVE => 'FWD',
        self::REAR_WHEEL_DRIVE  => 'RWD',
        self::ALL_WHEEL_DRIVE   => 'AWD',
    ];
}
