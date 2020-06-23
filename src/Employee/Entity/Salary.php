<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 * @ORM\Table(name="employee_salary")
 */
class Salary
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="salary_id")
     */
    private SalaryId $id;

    /**
     * @ORM\Column(type="employee_id")
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
