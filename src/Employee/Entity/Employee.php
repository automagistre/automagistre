<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Costil;
use App\Customer\Entity\OperandId;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFired;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Tenant\Entity\TenantEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"personId", "firedAt"}, message="Данный человек уже является работником", ignoreNull=false)
 */
class Employee extends TenantEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    private EmployeeId $id;

    /**
     * @ORM\Column(nullable=false)
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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(EmployeeId $employeeId = null)
    {
        $this->id = $employeeId ?? EmployeeId::generate();
        $this->hiredAt = new DateTime();

        $this->record(new EmployeeCreated($this->id));
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
        return null !== $employee && $employee->toId()->equals($this->id);
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

        $this->record(new EmployeeFired($this->id));
    }
}
