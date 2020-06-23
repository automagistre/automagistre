<?php

declare(strict_types=1);

namespace App\User\Entity;

class UserView
{
    public UserId $id;

    public string $username;

    public ?string $lastName = null;

    public ?string $firstName = null;

    public function __construct(UserId $id, string $username, ?string $lastName, ?string $firstName)
    {
        $this->id = $id;
        $this->username = $username;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
    }

    public function toString(): string
    {
        if (null !== $this->lastName && null !== $this->firstName) {
            return $this->lastName.' '.$this->firstName;
        }

        return $this->username;
    }
}
