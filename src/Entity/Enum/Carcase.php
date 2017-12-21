<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Carcase extends Enum
{
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

    protected static $name = [
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
