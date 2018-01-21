<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, EquatableInterface
{
    use Identity;

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
     * @ORM\OneToMany(targetEntity="App\Entity\UserCredentials", mappedBy="user", cascade={"persist", "remove"})
     */
    private $credentials;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Person", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $person;

    public function __construct(Person $person = null)
    {
        $this->credentials = new ArrayCollection();
        $this->person = $person;
    }

    public function setPerson(Person $person): void
    {
        if ($this->person) {
            throw new \DomainException('Person already defined for this user');
        }

        $this->person = $person;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function getFirstname(): ?string
    {
        return $this->person ? $this->person->getFirstname() : null;
    }

    public function getLastname(): ?string
    {
        return $this->person ? $this->person->getLastname() : null;
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

    public function getPassword(): ?string
    {
        $credential = $this->getCredential('password');

        return $credential ? $credential->getIdentifier() : null;
    }

    public function setPassword(string $password, PasswordEncoderInterface $encoder): void
    {
        if ($credential = $this->getCredential('password')) {
            $credential->expire();
        }

        $encoded = $encoder->encodePassword($password, $this->getSalt());

        $this->credentials[] = new UserCredentials($this, 'password', $encoded);
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

    /**
     * @TODO Implement Serializable
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($user->getUsername() !== $this->username) {
            return false;
        }

        if (($person = $user->getPerson()) !== $this->person) {
            return false;
        }

        if ($person && $person->getId() !== $this->person->getId()) {
            return false;
        }

        return true;
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
