<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\EmployeeId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class OrderInfo
{
    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    private ?OperandId $customerId;

    /**
     * @ORM\Column(type="car_id", nullable=true)
     */
    private ?CarId $carId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="employee_id", nullable=true)
     */
    private ?EmployeeId $workerId;

    public function __construct(?OperandId $customerId, ?CarId $carId, ?string $description, ?EmployeeId $workerId)
    {
        $this->customerId = $customerId;
        $this->carId = $carId;
        $this->description = $description;
        $this->workerId = $workerId;
    }
}
