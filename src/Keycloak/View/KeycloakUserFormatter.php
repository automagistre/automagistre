<?php

declare(strict_types=1);

namespace App\Keycloak\View;

use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\Keycloak\Entity\UserId;
use Keycloak\Admin\KeycloakClient;
use Premier\Identifier\Identifier;
use function array_key_exists;
use function Sentry\captureMessage;
use function sprintf;

final class KeycloakUserFormatter implements IdentifierFormatterInterface
{
    public function __construct(private KeycloakClient $keycloak)
    {
    }

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
        $user = $this->keycloak->getUser(['id' => $identifier->toString()]);

        if (array_key_exists('error', $user)) {
            captureMessage(sprintf('Keycloak return "%s" for id "%s"', $user['error'], $identifier->toString()));

            return 'error error';
        }

        return match (true) {
            !array_key_exists('firstName', $user) => $user['username'],
            !array_key_exists('lastName', $user) => sprintf('%s (%s)', $user['firstName'], $user['username']),
            default => sprintf('%s %s', $user['firstName'], $user['lastName']),
        };
    }
}
