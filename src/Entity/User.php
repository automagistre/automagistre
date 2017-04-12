<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Person", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $person;

    public function __construct(Person $person = null)
    {
        parent::__construct();

        $this->person = $person;
    }

    public function setPerson(Person $person): void
    {
        if ($this->person) {
            throw new \DomainException();
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

    public function setEmail($email)
    {
        $this->username = $email;

        return parent::setEmail($email);
    }
}
