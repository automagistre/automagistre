<?php

declare(strict_types=1);

namespace App\User\Security;

use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

final class ConsoleToken extends AbstractToken
{
    public function __construct(User $user)
    {
        parent::__construct($user->getRoles());

        $this->setAuthenticated(true);
        $this->setUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return [];
    }
}
