<?php

declare(strict_types=1);

namespace App\Utils;

use function array_keys;
use function count;
use function range;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ArrayUtils
{
    public static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
