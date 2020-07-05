<?php

declare(strict_types=1);

namespace App\User\Security;

use App\Shared\Doctrine\Registry;
use App\User\Entity\User;
use App\User\Entity\UserId;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ConsoleAuthenticator
{
    private Registry $registry;

    private TokenStorageInterface $storage;

    public function __construct(Registry $registry, TokenStorageInterface $storage)
    {
        $this->registry = $registry;
        $this->storage = $storage;
    }

    public function authenticate(UserId $userId): void
    {
        $user = $this->registry->get(User::class, $userId);

        $token = new ConsoleToken($user);

        $this->storage->setToken($token);
    }

    public function invalidate(): void
    {
        $this->storage->setToken(null);
    }
}
