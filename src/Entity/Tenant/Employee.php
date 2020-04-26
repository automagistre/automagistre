<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Customer\Domain\Person;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\PersonRelation;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"person.id", "firedAt"}, message="Данный человек уже является сотрудником", ignoreNull=false)
 */
class Employee
{
    use Identity;

    /**
     * @var PersonRelation
     *
     * @ORM\Embedded(class=PersonRelation::class)
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
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firedAt;

    public function __construct()
    {
        $this->person = new PersonRelation();
        $this->hiredAt = new DateTime();
    }

    public function __toString(): string
    {
        return $this->getPerson()->getFullName();
    }

    public function isEqual(?self $employee): bool
    {
        return null !== $employee && $this->getId() === $employee->getId();
    }

    public function setPerson(Person $person): void
    {
        $this->person = new PersonRelation($person);
    }

    public function getPerson(): ?Person
    {
        return $this->person->entityOrNull();
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
        return $this->getPerson()->getFullName();
    }

    public function isFired(): bool
    {
        return null !== $this->firedAt;
    }

    public function fire(): void
    {
        $this->person->entity()->setContractor(false);
        $this->firedAt = new DateTime();
    }
}
