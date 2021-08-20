<?php

declare(strict_types=1);

namespace App\Order\EventListener;

use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\Reservation;
use App\Order\Enum\OrderStatus;
use App\Order\Manager\ReservationManager;
use App\Part\Event\PartReserved;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatusListener implements MessageHandler
{
    public function __construct(private Registry $registry, private ReservationManager $reservationManager)
    {
    }

    public function __invoke(PartReserved $event): void
    {
        $reservation = $this->registry->find(Reservation::class, $event->reservationId);

        if (null === $reservation) {
            return;
        }

        $order = $reservation->getOrderItemPart()->getOrder();

        if (!$order->getStatus()->eq(OrderStatus::tracking())) {
            return;
        }

        /** @var OrderItemPart $item */
        foreach ($order->getItems(OrderItemPart::class) as $item) {
            $required = $item->getQuantity();
            $reserved = $this->reservationManager->reserved($item);

            if ($reserved < $required) {
                return;
            }

            $reservable = $this->reservationManager->reservable($item->getPartId());

            if (($reserved + $reservable) < $required) {
                return;
            }
        }

        $order->setStatus(OrderStatus::notification());
    }
}
