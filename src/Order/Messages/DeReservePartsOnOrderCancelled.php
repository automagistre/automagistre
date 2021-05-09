<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderStorage;
use App\Order\Exception\ReservationException;
use App\Order\Manager\ReservationManager;

final class DeReservePartsOnOrderCancelled implements MessageHandler
{
    public function __construct(private OrderStorage $storage, private ReservationManager $reservationManager)
    {
    }

    public function __invoke(OrderCancelled $event): void
    {
        $order = $this->storage->get($event->orderId);

        foreach ($order->getItems() as $item) {
            if (!$item instanceof OrderItemPart) {
                continue;
            }

            try {
                $this->reservationManager->deReserve($item);
            } catch (ReservationException $e) {
                // Nothing to do
            }
        }
    }
}
