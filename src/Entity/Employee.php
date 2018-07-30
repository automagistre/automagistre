<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"person"}, message="Данный человек уже является сотрудником")
 */
class Employee
{
    use Identity;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Person")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ratio;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $hiredAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firedAt;

    public function __construct()
    {
        $this->hiredAt = new DateTime();
    }

    public function __toString(): string
    {
        return $this->person->getFullName();
    }

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setRatio(int $ratio): void
    {
        $this->ratio = $ratio;
    }

    public function getRatio(): ?int
    {
        return $this->ratio;
    }

    public function getHiredAt(): DateTime
    {
        return $this->hiredAt;
    }

    public function getFiredAt(): ?DateTime
    {
        return $this->firedAt;
    }

    public function getFullName(): string
    {
        return $this->person->getFullName();
    }

    public function fire(): void
    {
        $this->firedAt = new DateTime();
    }
}
