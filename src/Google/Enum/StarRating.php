<?php

declare(strict_types=1);

namespace App\Google\Enum;

use Premier\Enum\Enum;

/**
 * @method static self fromGoogleValue(string $enum)
 */
final class StarRating extends Enum
{
    private const STAR_RATING_UNSPECIFIED = 0;
    private const ONE = 1;
    private const TWO = 2;
    private const THREE = 3;
    private const FOUR = 4;
    private const FIVE = 5;

    private static array $description = [
        self::STAR_RATING_UNSPECIFIED => 'Not specified.',
        self::ONE => 'One star out of a maximum of five.',
        self::TWO => 'Two stars out of a maximum of five.',
        self::THREE => 'Three stars out of a maximum of five.',
        self::FOUR => 'Four stars out of a maximum of five.',
        self::FIVE => 'The maximum star rating.',
    ];

    private static array $googleValue = [
        self::STAR_RATING_UNSPECIFIED => 'STAR_RATING_UNSPECIFIED',
        self::ONE => 'ONE',
        self::TWO => 'TWO',
        self::THREE => 'THREE',
        self::FOUR => 'FOUR',
        self::FIVE => 'FIVE',
    ];
}
