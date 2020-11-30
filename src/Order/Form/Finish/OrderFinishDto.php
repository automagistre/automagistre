<?php

declare(strict_types=1);

namespace App\Order\Form\Finish;

use App\Car\Entity\CarId;
use App\Car\Form\Mileage\CarMileageDto;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItemService;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrderFinishDto
{
    public OrderId $orderId;

    /**
     * @var OrderItemService[]
     */
    public $services;

    /**
     * @Assert\Valid
     */
    public ?CarMileageDto $mileage;

    public function __construct(OrderId $orderId, ?CarId $carId)
    {
        $this->orderId = $orderId;
        $this->mileage = null === $carId ? null : new CarMileageDto($carId);
    }
}
