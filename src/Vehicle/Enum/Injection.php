<?php

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

final class Injection extends Enum
{
    private const CLASSIC = 1;
    private const DIRECT = 2;

    protected static array $name = [
        self::CLASSIC => 'Классический',
        self::DIRECT => 'Непосредственный впрыск',
    ];
}
