<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\Employee;

/**
 * @psalm-suppress MissingConstructor
 */
final class OrderDto
{
    /**
     * @var null|CarId
     */
    public $carId;

    /**
     * @var null|OperandId
     */
    public $customerId;

    /**
     * @var null|int
     */
    public $mileage;

    /**
     * @var null|Employee
     */
    public $worker;

    /**
     * @var null|string
     */
    public $description;
}
