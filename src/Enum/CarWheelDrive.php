<?php

declare(strict_types=1);

namespace App\Enum;

use Premier\Enum\Enum;

/**
 * @method static self unknown()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarWheelDrive extends Enum
{
    private const UNKNOWN = 0;
    private const FRONT_WHEEL_DRIVE = 1;
    private const REAR_WHEEL_DRIVE = 2;
    private const ALL_WHEEL_DRIVE = 3;

    /**
     * @var array
     */
    protected static $name = [
        self::UNKNOWN => 'Неопределён',
        self::FRONT_WHEEL_DRIVE => 'Передний',
        self::REAR_WHEEL_DRIVE => 'Задний',
        self::ALL_WHEEL_DRIVE => 'Полный',
    ];

    /**
     * @var array
     */
    protected static $code = [
        self::UNKNOWN => '-',
        self::FRONT_WHEEL_DRIVE => 'FWD',
        self::REAR_WHEEL_DRIVE => 'RWD',
        self::ALL_WHEEL_DRIVE => 'AWD',
    ];

    public function getCode(): string
    {
        return self::$code[$this->getId()];
    }
}
