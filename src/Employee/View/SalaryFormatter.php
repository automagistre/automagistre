<?php

declare(strict_types=1);

namespace App\Employee\View;

use App\Employee\Entity\SalaryId;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class SalaryFormatter implements IdentifierFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        // TODO
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return SalaryId::class;
    }
}
