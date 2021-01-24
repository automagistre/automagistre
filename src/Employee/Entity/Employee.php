<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Costil;
use App\Customer\Entity\OperandId;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"personId", "firedAt"}, message="Данный человек уже является работником", ignoreNull=false)
 */
class Employee
{
    /**
     * @ORM\Id
     * @ORM\Column(type="employee_id")
     */
    private EmployeeId $id;

    /**
     * @ORM\Column(type="operand_id")
     */
    private ?OperandId $personId = null;

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
     * @var null|DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firedAt;

    public function __construct(EmployeeId $employeeId = null)
    {
        $this->id = $employeeId ?? EmployeeId::generate();
        $this->hiredAt = new DateTime();
    }

    public function __toString(): string
    {
        return Costil::display($this->toPersonId());
    }

    public function toId(): EmployeeId
    {
        return $this->id;
    }

    public function isEqual(?self $employee): bool
    {
        return null !== $employee && $employee->toId()->equal($this->id);
    }

    public function setPersonId(OperandId $personId): void
    {
        $this->personId = $personId;
    }

    public function toPersonId(): OperandId
    {
        if (null === $this->personId) {
            throw new LogicException('Need define PersonId first.');
        }

        return $this->personId;
    }

    public function getPersonId(): ?OperandId
    {
        return $this->personId;
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

    public function isFired(): bool
    {
        return null !== $this->firedAt;
    }

    public function fire(): void
    {
        $this->firedAt = new DateTime();
    }
}
