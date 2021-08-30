<?php

declare(strict_types=1);

namespace App\Employee\View;

use App\Employee\Entity\SalaryId;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use Premier\Identifier\Identifier;

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
