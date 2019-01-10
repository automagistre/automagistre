<?php

declare(strict_types=1);

namespace App;

use App\Entity\Landlord\User;
use App\Enum\Tenant;
use App\Tenant\ConnectionSwitcher;
use LogicException;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class State
{
    /**
     * @var Tenant
     */
    private $tenant;

    /**
     * @var ConnectionSwitcher
     */
    private $switcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

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
        }

        return $this->tenant;
    }

    public function user(): User
    {
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
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new LogicException('User expected.');
        }

        return $user;
    }
}
