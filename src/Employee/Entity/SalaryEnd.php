<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="employee_salary_end")
 */
class SalaryEnd extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=Salary::class, inversedBy="end")
     */
    private Salary $salary;

    public function __construct(Salary $salary)
    {
        $this->id = Uuid::uuid6();
        $this->salary = $salary;
    }
}
