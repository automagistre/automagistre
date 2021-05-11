<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\CreatedBy\Entity\CreatedByView;
use App\Customer\Entity\OperandId;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="salary_view")
 *
 * @psalm-suppress MissingConstructor
 */
class SalaryView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="salary_id")
     */
    public SalaryId $id;

    /**
     * @ORM\Column(type="employee_id")
     */
    public EmployeeId $employeeId;

    /**
     * @ORM\Column(type="operand_id")
     */
    public OperandId $personId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $payday;

    /**
     * @ORM\Column(type="money")
     */
    public Money $amount;

    /**
     * @ORM\Column(type="created_by_view")
     */
    public CreatedByView $created;

    /**
     * @ORM\Column(type="created_by_view")
     */
    public ?CreatedByView $ended = null;
}
