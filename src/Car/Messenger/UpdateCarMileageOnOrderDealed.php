<?php

declare(strict_types=1);

namespace App\Car\Messenger;

use App\Car\Entity\Car;
use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Order\Messages\OrderDealed;
use App\Shared\Doctrine\Registry;

final class UpdateCarMileageOnOrderDealed implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(OrderDealed $event): void
    {
        $order = $this->registry->get(Order::class, $event->orderId);

        $carId = $order->getCarId();
        $mileage = $order->getMileage();

        if (null === $carId || null === $mileage) {
            return;
        }

        $car = $this->registry->get(Car::class, $carId);
        $car->mileage = $mileage;
    }
}
