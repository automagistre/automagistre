<?php

declare(strict_types=1);

namespace App\Storage\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Manager\ReservationManager;
use App\Order\Messages\OrderDealed;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\Part;
use App\Storage\Enum\Source;

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

            if (0 !== $this->reservationManager->reserved($item)) {
                $this->reservationManager->deReserve($item, $quantity);
            }

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $this->registry->add($storagePart);
            }

            $storagePart->move(0 - $quantity, Source::order(), $order->toId()->toUuid());
        }
    }
}
