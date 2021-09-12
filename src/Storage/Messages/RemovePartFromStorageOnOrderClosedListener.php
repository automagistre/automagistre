<?php

declare(strict_types=1);

namespace App\Storage\Messages;

use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Manager\ReservationManager;
use App\Order\Messages\OrderDealed;
use App\Storage\Entity\MotionSource;
use App\Storage\Entity\Part;

final class RemovePartFromStorageOnOrderClosedListener implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private ReservationManager $reservationManager,
    ) {
    }

    public function __invoke(OrderDealed $event): void
    {
        $order = $this->registry->get(Order::class, $event->orderId);

        foreach ($order->getItems(OrderItemPart::class) as $item) {
            /** @var OrderItemPart $item */
            $partId = $item->getPartId();
            $quantity = $item->getQuantity();

            if (0 === $quantity || $quantity < 0) {
                continue;
            }

            if (0 !== $this->reservationManager->reserved($item)) {
                $this->reservationManager->deReserve($item, $quantity);
            }

            $storagePart = $this->registry->get(Part::class, $partId);

            $storagePart->decrease($quantity, MotionSource::order($order->toId()));
        }
    }
}
