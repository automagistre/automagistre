<?php

declare(strict_types=1);

namespace App\Employee\Messages;

use App\Employee\Entity\EmployeeStorage;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderStorage;
use App\Order\Messages\OrderClosed;

final class OrderClosedHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    private EmployeeStorage $employeeStorage;

    public function __construct(OrderStorage $orderStorage, EmployeeStorage $employeeStorage)
    {
        $this->orderStorage = $orderStorage;
        $this->employeeStorage = $employeeStorage;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        foreach ($order->getItems(OrderItemService::class) as $item) {
            /** @var OrderItemService $item */
            $price = $item->getTotalPrice(true, false);
            if (!$price->isPositive()) {
                continue;
            }

            $workerId = $item->workerId;
            if (null === $workerId) {
                continue;
            }

            $employee = $this->employeeStorage->chargeable($workerId);
            if (null === $employee) {
                continue;
            }

            $employee->chargeByOrder($order->toId(), $price);
        }
    }
}
