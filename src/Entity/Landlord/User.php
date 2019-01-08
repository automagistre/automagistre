<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use DomainException;
use Serializable;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, EquatableInterface, Serializable
{
    use Identity;
    use Uuid;

    public const PASSWORD_CREDENTIALS_TYPE = 'password';

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles = [];

    /**
     * @var string
     *
     * @Assert\Email
     * @Assert\NotBlank
     *
     * @ORM\Column(unique=true)
     */
    private $username;

    /**
     * @var UserCredentials[]|ArrayCollection|PersistentCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\UserCredentials", mappedBy="user", cascade={"persist", "remove"})
     */
    private $credentials;

    /**
     * @var Person|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Landlord\Person", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $person;

    public function __construct(Person $person = null)
    {
        $this->generateUuid();
        $this->credentials = new ArrayCollection();
        $this->person = $person;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function setPerson(Person $person): void
    {
        if ($this->person instanceof Person) {
            throw new DomainException('Person already defined for this user');
        }

        $this->person = $person;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function getFirstname(): ?string
    {
        return $this->person instanceof Person ? $this->person->getFirstname() : null;
    }

    public function getLastname(): ?string
    {
        return $this->person instanceof Person ? $this->person->getLastname() : null;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function getPassword(): ?string
    {
        $credential = $this->getCredential(self::PASSWORD_CREDENTIALS_TYPE);

        return $credential instanceof UserCredentials ? $credential->getIdentifier() : null;
    }

    public function changePassword(string $password, PasswordEncoderInterface $encoder): void
    {
        if (null !== $credential = $this->getCredential(self::PASSWORD_CREDENTIALS_TYPE)) {
            $credential->expire();
        }

        $encoded = $encoder->encodePassword($password, $this->getSalt());

        $this->credentials[] = new UserCredentials($this, self::PASSWORD_CREDENTIALS_TYPE, $encoded);
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
        return \serialize([
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
        ] = \unserialize($serialized, ['allowed_classes' => true]);

        $this->roles = $roles ?? [];
        $this->credentials = new ArrayCollection();
    }

    private function getCredential(string $type): ?UserCredentials
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('type', $type))
            ->andWhere(Criteria::expr()->isNull('expiredAt'));

        if ($this->credentials instanceof PersistentCollection && !$this->credentials->isInitialized()) {
            $this->credentials->initialize();
        }

        $collection = $this->credentials->matching($criteria);

        if ($collection->isEmpty()) {
            return null;
        }

        return $collection->first();
    }
}
