<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function array_unique;
use function in_array;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"username", "tenant_id"})
 *     }
 * )
 */
class User extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="user_id")
     */
    public UserId $id;

    /**
     * @ORM\Column(length=32, nullable=true)
     */
    public ?string $firstName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $lastName = null;

    /**
     * @ORM\Column(type="json")
     */
    protected array $roles = [];

    /**
     * @Assert\Email
     * @Assert\NotBlank
     *
     * @ORM\Column()
     */
    private string $username;

    public function __construct(UserId $userId, array $roles, string $username)
    {
        $this->id = $userId;
        $this->roles = $roles;
        $this->username = $username;
    }

    public function toId(): UserId
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
