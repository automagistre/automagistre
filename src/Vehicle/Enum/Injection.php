<?php

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

final class Injection extends Enum
{
    private const MONO = 1;
    private const DIRECT = 2;

    protected static array $name = [
        self::MONO => 'Моновпрыск',
        self::DIRECT => 'Непосредственный впрыск',
    ];
}
