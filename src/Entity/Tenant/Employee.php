<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"person.uuid", "firedAt"}, message="Данный человек уже является сотрудником", ignoreNull=false)
 */
class Employee
{
    use Identity;

    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
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
        $this->person = new OperandRelation();
        $this->hiredAt = new DateTime();
    }

    public function __toString(): string
    {
        return $this->getPerson()->getFullName();
    }

    public function setPerson(Person $person): void
    {
        $this->person = new OperandRelation($person);
    }

    public function getPerson(): ?Person
    {
        if ($this->person->isEmpty()) {
            return null;
        }

        $entity = $this->person->entity();
        if (!$entity instanceof Person) {
            throw new LogicException('Person expected');
        }

        return $entity;
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
