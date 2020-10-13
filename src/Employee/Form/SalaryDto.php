<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Employee\Entity\EmployeeId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class SalaryDto
{
    /**
     * @var EmployeeId
     *
     * @Assert\NotBlank
     */
    public $employeeId;

    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $payday;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $amount;
}
