<?php

declare(strict_types=1);

namespace App\Keycloak\Security;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class KeycloakUser implements UserInterface
{
    public function __construct(
        private string $id,
        public string $username,
        public AccessTokenInterface $accessToken,
        public array $roles = [],
        public ?string $email = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public array $attributes = [],
    ) {
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->id !== $user->id) {
            return false;
        }

        return true;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
