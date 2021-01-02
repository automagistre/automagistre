<?php

declare(strict_types=1);

namespace App\Review\Enum;

use Premier\Enum\Enum;

/**
 * @method static self manual()
 * @method static self yandex()
 * @method static self google()
 * @method static self twoGis()
 * @method string toDisplayName()
 */
final class ReviewSource extends Enum
{
    private const MANUAL = 1;
    private const YANDEX = 2;
    private const GOOGLE = 3;
    private const TWO_GIS = 4;

    private static array $name = [
        self::MANUAL => 'club',
        self::YANDEX => 'yandex',
        self::GOOGLE => 'google',
        self::TWO_GIS => 'two_gis',
    ];

    private static array $displayName = [
        self::MANUAL => 'Manual',
        self::YANDEX => 'Yandex',
        self::GOOGLE => 'Google',
        self::TWO_GIS => '2GIS',
    ];
}
