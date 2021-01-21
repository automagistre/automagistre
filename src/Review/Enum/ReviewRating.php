<?php

declare(strict_types=1);

namespace App\Review\Enum;

use Premier\Enum\Enum;

/**
 * @method static self unspecified()
 * @method static self fromGoogleValue(string $enum)
 */
final class ReviewRating extends Enum
{
    private const UNSPECIFIED = 0;
    private const ONE = 1;
    private const TWO = 2;
    private const THREE = 3;
    private const FOUR = 4;
    private const FIVE = 5;

    private static array $googleValue = [
        self::UNSPECIFIED => 'STAR_RATING_UNSPECIFIED',
        self::ONE => 'ONE',
        self::TWO => 'TWO',
        self::THREE => 'THREE',
        self::FOUR => 'FOUR',
        self::FIVE => 'FIVE',
    ];
}
