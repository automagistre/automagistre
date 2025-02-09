<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="employee_salary")
 */
class Salary extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private SalaryId $id;

    /**
     * @ORM\Column
     */
    private EmployeeId $employeeId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $payday;

    /**
     * @ORM\Column(type="money")
     */
    private Money $amount;

    /**
     * @ORM\OneToOne(targetEntity=SalaryEnd::class, mappedBy="salary", cascade={"persist"})
     */
    private ?SalaryEnd $end;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(SalaryId $salaryId, EmployeeId $employeeId, int $payday, Money $amount)
    {
        $this->id = $salaryId;
        $this->employeeId = $employeeId;
        $this->payday = $payday;
        $this->amount = $amount;
        $this->end = null;
    }

    public function toId(): SalaryId
    {
        return $this->id;
    }

    public function end(): void
    {
        $this->end = new SalaryEnd($this);
    }
}
