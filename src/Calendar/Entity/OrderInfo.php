<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\EmployeeId;
use App\Shared\Identifier\Identifier;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 *
 * @ORM\Embeddable
 */
final class OrderInfo
{
    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    public ?OperandId $customerId;

    /**
     * @ORM\Column(type="car_id", nullable=true)
     */
    public ?CarId $carId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $description;

    /**
     * @ORM\Column(type="employee_id", nullable=true)
     */
    public ?EmployeeId $workerId;

    public function __construct(?OperandId $customerId, ?CarId $carId, ?string $description, ?EmployeeId $workerId)
    {
        $this->customerId = $customerId;
        $this->carId = $carId;
        $this->description = $description;
        $this->workerId = $workerId;
    }

    public function equal(self $right): bool
    {
        $left = $this;

        return
            Identifier::same($left->customerId, $right->customerId)
            && Identifier::same($left->carId, $right->carId)
            && Identifier::same($left->workerId, $right->workerId)
            && $left->description === $right->description;
    }
}
