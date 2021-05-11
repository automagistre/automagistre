<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Calendar\Entity\OrderInfo;
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
     * @var null|OperandId
     */
    public $customerId;

    /**
     * @var null|CarId
     */
    public $carId;

    /**
     * @var null|string
     */
    public $description;

    /**
     * @var null|EmployeeId
     */
    public $workerId;

    public function __construct(
        ?OperandId $customerId = null,
        ?CarId $carId = null,
        ?string $description = null,
        ?EmployeeId $workerId = null,
    ) {
        $this->customerId = $customerId;
        $this->carId = $carId;
        $this->description = $description;
        $this->workerId = $workerId;
    }

    public static function fromOrderInfo(OrderInfo $orderInfo): self
    {
        return new self(
            $orderInfo->customerId,
            $orderInfo->carId,
            $orderInfo->description,
            $orderInfo->workerId,
        );
    }
}
