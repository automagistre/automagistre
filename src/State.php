<?php

declare(strict_types=1);

namespace App;

use App\Tenant\Tenant;
use App\User\Entity\User;
use LogicException;
use RuntimeException;
use function Sentry\configureScope;
use Sentry\State\Scope;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class State
{
    private Tenant $tenant;

    private TokenStorageInterface $tokenStorage;

    private ?User $user;

    public function __construct(TokenStorageInterface $tokenStorage, Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->tokenStorage = $tokenStorage;

        configureScope(fn (Scope $scope) => $scope->setTag('tenant', $tenant->toIdentifier()));
    }

    public function tenant(): Tenant
    {
        return $this->tenant;
    }

    public function user(User $user = null): User
    {
        if ($user instanceof User) {
            return $this->user = $user;
        }

        $user = $this->userOrNull();
        if (!$user instanceof User) {
            throw new RuntimeException('User not exist.');
        }

        return $user;
    }

    public function userOrNull(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return $this->user;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new LogicException('User expected.');
        }

        return $user;
    }
}
