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
}
