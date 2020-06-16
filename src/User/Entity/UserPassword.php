<?php

declare(strict_types=1);

namespace App\User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users_password")
 */
class UserPassword
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="user_password_id")
     */
    private UserPasswordId $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="passwords")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    public function __construct(User $user, string $password)
    {
        $this->id = UserPasswordId::generate();
        $this->user = $user;
        $this->password = $password;
    }

    public function toPassword(): string
    {
        return $this->password;
    }
}
