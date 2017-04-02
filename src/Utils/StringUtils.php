<?php

namespace App\Utils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StringUtils
{
    public static function isRussian(string $text): bool
    {
        return (bool) preg_match('/[А-Яа-яЁё]/u', $text);
    }
}
