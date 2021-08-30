<?php

declare(strict_types=1);

namespace App\User\View;

use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\User\Entity\UserId;
use Premier\Identifier\Identifier;

final class DummyUserFormatter implements IdentifierFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return UserId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        return 'firstName lastName';
    }
}
