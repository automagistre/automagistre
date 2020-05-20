<?php

declare(strict_types=1);

namespace App\Shared\String;

use function array_keys;
use function array_values;
use function mb_ereg_match;
use function str_replace;

final class Layout
{
    private const EN_RU = [
        'q' => 'й',
        'Q' => 'й',
        'w' => 'ц',
        'W' => 'ц',
        'e' => 'у',
        'E' => 'у',
        'r' => 'к',
        'R' => 'к',
        't' => 'е',
        'T' => 'е',
        'y' => 'н',
        'Y' => 'н',
        'u' => 'г',
        'U' => 'г',
        'i' => 'ш',
        'I' => 'ш',
        'o' => 'щ',
        'O' => 'щ',
        'p' => 'з',
        'P' => 'з',
        '[' => 'х',
        '{' => 'х',
        ']' => 'ъ',
        '}' => 'ъ',
        'a' => 'ф',
        'A' => 'ф',
        's' => 'ы',
        'S' => 'ы',
        'd' => 'в',
        'D' => 'в',
        'f' => 'а',
        'F' => 'а',
        'g' => 'п',
        'G' => 'п',
        'h' => 'р',
        'H' => 'р',
        'j' => 'о',
        'J' => 'о',
        'k' => 'л',
        'K' => 'л',
        'l' => 'д',
        'L' => 'д',
        ';' => 'ж',
        '\'' => 'э',
        'z' => 'я',
        'Z' => 'я',
        'x' => 'ч',
        'X' => 'ч',
        'c' => 'с',
        'C' => 'с',
        'v' => 'м',
        'V' => 'м',
        'b' => 'и',
        'B' => 'и',
        'n' => 'т',
        'N' => 'т',
        'm' => 'ь',
        'M' => 'ь',
        ',' => 'б',
        '.' => 'ю',
        '/' => '.',
        '`' => 'ё',
        '~' => 'Ё',
        '@' => '"',
        '#' => '№',
        '$' => ';',
        '^' => ':',
        '&' => '?',
        '|' => '/',
        ':' => 'Ж',
        '"' => 'Э',
        '<' => 'Б',
        '>' => 'Ю',
        '?' => ',',
    ];
    private const RU_EN = [
        'й' => 'q',
        'Й' => 'Q',
        'ц' => 'w',
        'Ц' => 'W',
        'у' => 'e',
        'У' => 'e',
        'к' => 'r',
        'К' => 'R',
        'е' => 't',
        'Е' => 'T',
        'н' => 'y',
        'Н' => 'Y',
        'г' => 'u',
        'Г' => 'U',
        'ш' => 'i',
        'Ш' => 'I',
        'щ' => 'o',
        'Щ' => 'O',
        'з' => 'p',
        'З' => 'P',
        '[' => 'х',
        'Х' => 'X',
        ']' => 'ъ',
        'Ъ' => '}',
        'ф' => 'a',
        'Ф' => 'A',
        'ы' => 's',
        'Ы' => 'S',
        'в' => 'd',
        'В' => 'D',
        'а' => 'f',
        'А' => 'F',
        'п' => 'g',
        'П' => 'G',
        'р' => 'h',
        'Р' => 'H',
        'о' => 'j',
        'О' => 'J',
        'л' => 'k',
        'Л' => 'K',
        'д' => 'l',
        'Д' => 'L',
        ';' => 'ж',
        'Ж' => ':',
        '\'' => 'э',
        'Э' => '"',
        'я' => 'z',
        'Я' => 'Z',
        'ч' => 'x',
        'Ч' => 'X',
        'с' => 'c',
        'С' => 'C',
        'м' => 'v',
        'М' => 'V',
        'и' => 'b',
        'И' => 'B',
        'т' => 'n',
        'Т' => 'N',
        'ь' => 'm',
        'Ь' => 'M',
        'б' => ',',
        'Б' => '<',
        'Ю' => '>',
        '/' => '.',
        '`' => 'ё',
        'Ё' => '~',
        '~' => 'Ё',
        '@' => '"',
        '#' => '№',
        '$' => ';',
        '^' => ':',
        '&' => '?',
        '|' => '/',
        ':' => 'Ж',
        '"' => 'Э',
        '<' => 'Б',
        '>' => 'Ю',
        '?' => ',',
    ];

    public static function switch(string $text): string
    {
        return self::convert($text, self::isRussian($text) ? self::RU_EN : self::EN_RU);
    }

    public static function english(string $text): string
    {
        return self::convert($text, self::RU_EN);
    }

    private static function convert(string $text, array $dict): string
    {
        return str_replace(array_keys($dict), array_values($dict), $text);
    }

    private static function isRussian(string $text): bool
    {
        return mb_ereg_match('([а-яА-Я@.$+\-*!#%\^&()=<>\?]+)', $text);
    }
}
