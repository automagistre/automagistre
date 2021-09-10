<?php

declare(strict_types=1);

namespace App\Keycloak\View;

use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\Keycloak\Entity\UserId;
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
