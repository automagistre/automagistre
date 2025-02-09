<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Salary $salary)
    {
        $this->id = Uuid::uuid6();
        $this->salary = $salary;
    }
}
