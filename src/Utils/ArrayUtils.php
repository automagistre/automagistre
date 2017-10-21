<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ArrayUtils
{
    public static function isAssoc($arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
