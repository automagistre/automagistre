<?php

declare(strict_types=1);

namespace App;

use App\Doctrine\ORM\Type\Identifier;
use App\Infrastructure\Identifier\IdentifierFormatter;

/**
 * Сборник костылей.
 */
final class Costil
{
    public const PODSTANOVA = 45;

    public static IdentifierFormatter $formatter;

    private function __construct()
    {
    }

    /**
     * Monkey migration. EasyAdminAutocompleteType require entity with __toString.
     */
    public static function display(Identifier $identifier, string $format = null): string
    {
        return self::$formatter->format($identifier, $format);
    }
}
