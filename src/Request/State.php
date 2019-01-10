<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\DBAL\SwitchableConnection;
use App\Entity\Landlord\User;
use App\Enum\Tenant;
use LogicException;
use RuntimeException;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    public function tenant(Tenant $tenant = null): Tenant
    {
        if (null !== $tenant) {
            $this->switch($tenant);
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

    private function switch(Tenant $tenant): void
    {
        $connection = $this->registry->getConnection('tenant');
        if (!$connection instanceof SwitchableConnection) {
            throw new LogicException('SwitchableConnection required');
        }

        $connection->switch($tenant->getDatabase());
    }
}
