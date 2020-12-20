<?php

declare(strict_types=1);

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

/**
 * @method static self unknown()
 * @method static self pickup()
 * @method string toDisplayName()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BodyType extends Enum
{
    private const UNKNOWN = 0;
    private const SEDAN = 1;
    private const HATCHBACK = 2;
    private const LIFTBACK = 3;
    private const ALLROAD = 4;
    private const WAGON = 5;
    private const COUPE = 6;
    private const MINIVAN = 7;
    private const PICKUP = 8;
    private const LIMOUSINE = 9;
    private const VAN = 10;
    private const CABRIO = 11;

    protected static array $name = [
        self::UNKNOWN => 'unknown',
        self::SEDAN => 'sedan',
        self::HATCHBACK => 'hatchback',
        self::LIFTBACK => 'liftback',
        self::ALLROAD => 'allroad',
        self::WAGON => 'wagon',
        self::COUPE => 'coupe',
        self::MINIVAN => 'minivan',
        self::PICKUP => 'pickup',
        self::LIMOUSINE => 'limousine',
        self::VAN => 'van',
        self::CABRIO => 'cabrio',
    ];

    protected static array $displayName = [
        self::UNKNOWN => 'Неопределён',
        self::SEDAN => 'Седан',
        self::HATCHBACK => 'Хэтчбек',
        self::LIFTBACK => 'Лифтбек',
        self::ALLROAD => 'Внедорожник',
        self::WAGON => 'Универсал',
        self::COUPE => 'Купе',
        self::MINIVAN => 'Минивэн',
        self::PICKUP => 'Пикап',
        self::LIMOUSINE => 'Лимузин',
        self::VAN => 'Фургон',
        self::CABRIO => 'Кабриолет',
    ];
}
