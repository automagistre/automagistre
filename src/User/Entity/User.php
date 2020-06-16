<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
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
    use Identity;

    /**
     * @ORM\Column(type="user_id", unique=true)
     */
    public UserId $uuid;

    /**
     * @ORM\Column(type="array")
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

    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    private ?OperandId $personId;

    public function __construct(UserId $userId, array $roles, string $username, ?OperandId $personId)
    {
        $this->uuid = $userId;
        $this->roles = $roles;
        $this->username = $username;
        $this->personId = $personId;
        $this->passwords = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function toId(): UserId
    {
        return $this->uuid;
    }

    public function setPersonId(OperandId $personId): void
    {
        if (null !== $this->personId && !$personId->equal($this->personId)) {
            throw new DomainException('Person already defined for this user');
        }

        $this->personId = $personId;
    }

    public function getPersonId(): ?OperandId
    {
        return $this->personId;
    }

    public function getRoles(): array
    {
        return $this->roles;
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
