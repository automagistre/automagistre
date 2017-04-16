<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
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

        $this->id = UuidGenerator::generate();
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
