<?php

declare(strict_types=1);

namespace App\Keycloak\Security;

use App\Keycloak\Constants;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use LogicException;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function array_diff_key;
use function array_flip;
use function Sentry\captureException;
use function sprintf;

final class KeycloakUserProvider implements UserProviderInterface
{
    public function __construct(
        private Keycloak $provider,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $identifier,
            'redirect_uri' => $this->urlGenerator->generate(Constants::REDIRECT_ROUTE, referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->createFromToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof KeycloakUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $accessToken = $user->accessToken;

        if ($accessToken->hasExpired()) {
            try {
                $token = $this->provider->getAccessToken(
                    'refresh_token',
                    [
                        'refresh_token' => $accessToken->getRefreshToken(),
                    ],
                );
            } catch (IdentityProviderException $e) {
                captureException($e);

                throw new UserNotFoundException($e->getMessage());
            }

            $user = $this->createFromToken($token);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return KeycloakUser::class === $class;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    private function createFromToken(AccessTokenInterface $token): KeycloakUser
    {
        if (!$token instanceof AccessToken) {
            throw new LogicException(sprintf('%s expected, but %s given', AccessToken::class, $token::class));
        }

        $user = $this->provider->getResourceOwner($token);
        $payload = $user->toArray();

        $allowedArguments = array_flip([
            'username',
            'roles',
            'email',
            'firstname',
            'lastname',
        ]);

        $arguments = array_intersect_key($payload, $allowedArguments);
        $arguments['id'] = $payload['sub'];
        $arguments['accessToken'] = $token;
        $arguments['attributes'] = array_diff_key($payload, $arguments);

        return new KeycloakUser(...$arguments);
    }
}
