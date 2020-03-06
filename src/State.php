<?php

declare(strict_types=1);

namespace App;

use App\Tenant\ConnectionSwitcher;
use App\Tenant\Tenant;
use App\User\Entity\User;
use LogicException;
use RuntimeException;
use Sentry\SentryBundle\SentryBundle;
use Sentry\State\Scope;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class State
{
    private ?Tenant $tenant = null;

    private ConnectionSwitcher $switcher;

    private TokenStorageInterface $tokenStorage;

    private ?User $user = null;

    public function __construct(ConnectionSwitcher $switcher, TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->switcher = $switcher;
    }

    public function tenant(Tenant $tenant = null): Tenant
    {
        if (null !== $tenant) {
            $this->switcher->switch($tenant);
            $this->tenant = $tenant;

            SentryBundle::getCurrentHub()
                ->configureScope(fn (Scope $scope) => $scope->setTag('tenant', $tenant->toIdentifier()));
        }

        if (null === $this->tenant) {
            throw new LogicException('Tenant must be defined before getting it.');
        }

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
