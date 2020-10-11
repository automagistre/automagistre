<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Shared\String\Layout;
use function preg_replace;
use function strtoupper;

/**
 * @psalm-immutable
 */
final class PartNumber
{
    public string $number;

    public function __construct(string $number)
    {
        $this->number = self::sanitize($number);
    }

    public function __toString(): string
    {
        return $this->number;
    }

    public static function sanitize(?string $number): string
    {
        if (null === $number) {
            return '';
        }

        $number = Layout::english($number);
        $number = preg_replace('/[^a-zA-Z0-9]/', '', $number);

        if (null === $number) {
            return '';
        }

        return strtoupper($number);
    }
}
