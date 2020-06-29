<?php

declare(strict_types=1);

namespace App\Nsq;

use function array_map;
use function implode;
use function strlen;
use function substr;
use function unpack;

final class Extractor
{
    private function __construct()
    {
    }

    public static function int32(string &$str, int $length): int
    {
        /** @psalm-suppress PossiblyInvalidArrayAccess */
        return unpack('N', self::cut($str, $length))[1];
    }

    public static function int64(string &$str, int $length): int
    {
        /** @psalm-suppress PossiblyInvalidArrayAccess */
        return unpack('q', self::cut($str, $length))[1];
    }

    public static function uInt16(string &$str, int $length): int
    {
        /** @psalm-suppress PossiblyInvalidArrayAccess */
        return unpack('n', self::cut($str, $length))[1];
    }

    public static function string(string &$str, int $length): string
    {
        $bytes = self::cut($str, $length);
        $size = strlen($bytes);
        $bytes = unpack("c{$size}chars", $bytes);

        /** @psalm-suppress PossiblyFalseArgument */
        return implode('', array_map('chr', $bytes));
    }

    private static function cut(string &$str, int $length): string
    {
        $substring = substr($str, 0, $length);
        $str = substr($str, $length);

        return $substring;
    }
}
