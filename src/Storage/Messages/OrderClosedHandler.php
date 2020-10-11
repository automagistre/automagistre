<?php

declare(strict_types=1);

namespace App\Storage\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderStorage;
use App\Order\Manager\ReservationManager;
use App\Order\Messages\OrderClosed;
use App\Storage\Entity\MotionStorage;
use App\Storage\Enum\Source;

final class OrderClosedHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    private ReservationManager $reservationManager;

    private MotionStorage $motionStorage;

    public function __construct(
        OrderStorage $orderStorage,
        ReservationManager $reservationManager,
        MotionStorage $motionStorage
    ) {
        $this->orderStorage = $orderStorage;
        $this->reservationManager = $reservationManager;
        $this->motionStorage = $motionStorage;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        foreach ($order->getItems(OrderItemPart::class) as $item) {
            /** @var OrderItemPart $item */
            $partId = $item->getPartId();
            $quantity = $item->getQuantity();

            if (0 !== $this->reservationManager->reserved($item)) {
                $this->reservationManager->deReserve($item, $quantity);
            }

            $motionalPart = $this->motionStorage->getPart($partId);
            $motionalPart->move(0 - $quantity, Source::order(), $order->toId()->toUuid());
        }
    }
}
