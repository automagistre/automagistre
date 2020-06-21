<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Entity\Embeddable\UserRelation;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\User\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class MonthlySalary
{
    use Identity;
    use CreatedAt;
    use CreatedByRelation;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $uuid;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private $employee;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $payday;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    private $amount;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $endedAt;

    /**
     * @var UserRelation|null
     *
     * @ORM\Embedded(class=UserRelation::class)
     */
    private $endedBy;

    public function __construct(Employee $employee, int $payday, Money $amount, User $user)
    {
        $this->uuid = Uuid::uuid6();
        $this->createdByRelation = new UserRelation($user);

        $this->employee = $employee;
        $this->payday = $payday;
        $this->amount = $amount;
    }

    public function isEnded(): bool
    {
        return null !== $this->endedAt;
    }

    public function end(User $user): void
    {
        if (null !== $this->endedAt || null !== $this->endedBy) {
            throw new LogicException('FixedSalary already ended.');
        }

        $this->endedAt = new DateTimeImmutable();
        $this->endedBy = new UserRelation($user);
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getPayday(): int
    {
        return $this->payday;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
