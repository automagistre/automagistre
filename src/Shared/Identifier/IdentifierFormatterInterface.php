<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use Premier\Identifier\Identifier;

interface IdentifierFormatterInterface
{
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string;

    /**
     * @psalm-return class-string
     */
    public static function support(): string;
}
