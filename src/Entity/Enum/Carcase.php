<?php

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Carcase extends Enum
{
    const SEDAN = 1;
    const HATCHBACK = 2;
    const LIFTBACK = 3;
    const ALLROAD = 4;
    const WAGON = 5;
    const COUPE = 6;
    const MINIVAN = 7;
    const PICKUP = 8;
    const LIMOUSINE = 9;
    const VAN = 10;
    const CABRIO = 11;

    protected static $name = [
        self::SEDAN     => 'Седан',
        self::HATCHBACK => 'Хэтчбек',
        self::LIFTBACK  => 'Лифтбек',
        self::ALLROAD   => 'Внедорожник',
        self::WAGON     => 'Универсал',
        self::COUPE     => 'Купе',
        self::MINIVAN   => 'Минивэн',
        self::PICKUP    => 'Пикап',
        self::LIMOUSINE => 'Лимузин',
        self::VAN       => 'Фургон',
        self::CABRIO    => 'Кабриолет',
    ];
}
