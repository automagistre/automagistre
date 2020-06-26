<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\Employee;

final class OrderDto
{
    /**
     * @var CarId|null
     */
    public $carId;

    /**
     * @var OperandId|null
     */
    public $customerId;

    /**
     * @var int|null
     */
    public $mileage;

    /**
     * @var Employee|null
     */
    public $worker;

    /**
     * @var string|null
     */
    public $description;

    private function __construct(
        ?CarId $carId,
        ?OperandId $customerId,
        ?int $mileage,
        ?Employee $worker,
        ?string $description
    ) {
        $this->carId = $carId;
        $this->customerId = $customerId;
        $this->mileage = $mileage;
        $this->worker = $worker;
        $this->description = $description;
    }
}
