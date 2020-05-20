<?php

namespace App\Vehicle\Enum;

use Premier\Enum\Enum;

final class AirIntake extends Enum
{
    private const ATMOSPHERIC = 1;
    private const TURBO = 2;

    protected static array $name = [
        self::ATMOSPHERIC => 'Атмосферный',
        self::TURBO => 'Турбированный',
    ];
}
