<?php

declare(strict_types=1);

namespace App\User\Entity;

use function array_unique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function in_array;
use Serializable;
use function serialize;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use function unserialize;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, EquatableInterface, Serializable
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
     * @ORM\Column(unique=true)
     */
    private string $username;

    /**
     * @var Collection<int, UserPassword>
     *
     * @ORM\OneToMany(targetEntity=UserPassword::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private Collection $passwords;

    public function __construct(UserId $userId, array $roles, string $username)
    {
        $this->id = $userId;
        $this->roles = $roles;
        $this->username = $username;
        $this->passwords = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null !== $this->lastName && null !== $this->firstName) {
            return $this->lastName.' '.$this->firstName;
        }

        return $this->username;
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

    public function getPassword(): ?string
    {
        $userPassword = $this->passwords->last();

        return false === $userPassword ? null : $userPassword->toPassword();
    }

    public function changePassword(string $password, PasswordEncoderInterface $encoder): void
    {
        $encoded = $encoder->encodePassword($password, $this->getSalt());

        $this->passwords[] = new UserPassword($this, $encoded);
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $right): bool
    {
        if (!$right instanceof self) {
            return false;
        }

        $left = $this;

        if ($right->getUsername() !== $left->getUsername()) {
            return false;
        }

        if ($right->getRoles() !== $left->getRoles()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->roles,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $roles,
        ] = unserialize($serialized, ['allowed_classes' => true]);

        $this->roles = $roles ?? [];
        $this->passwords = new ArrayCollection();
    }
}
