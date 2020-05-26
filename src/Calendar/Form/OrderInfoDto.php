<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\EmployeeId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\Expression(
 *     "this.customerId != null or this.carId != null or this.description != null or this.customer != null",
 *     message="Нужно заполнить хотя бы одно из полей"
 * )
 */
final class OrderInfoDto
{
    /**
     * @var OperandId|null
     */
    public $customerId;

    /**
     * @var CarId|null
     */
    public $carId;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var EmployeeId|null
     */
    public $workerId;

    /**
     * @Assert\Valid()
     */
    public ?PersonDto $customer = null;

    public function __construct(
        ?OperandId $customerId = null,
        ?CarId $carId = null,
        ?string $description = null,
        ?EmployeeId $workerId = null,
        ?PersonDto $customer = null
    ) {
        $this->customerId = $customerId;
        $this->carId = $carId;
        $this->description = $description;
        $this->workerId = $workerId;
        $this->customer = $customer;
    }
}
