<?php

declare(strict_types=1);

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

    public static function underscore(string $string, int $case = CASE_LOWER): string
    {
        $string = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);

        if (CASE_UPPER === $case) {
            return strtoupper($string);
        }

        return strtolower($string);
    }
}
