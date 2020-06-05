<?php

declare(strict_types=1);

namespace App\Order\EventListener;

use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\Reservation;
use App\Order\Enum\OrderStatus;
use App\Order\Manager\ReservationManager;
use App\Part\Event\PartReserved;
use App\Shared\Doctrine\Registry;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatusListener implements EventSubscriberInterface
{
    private Registry $registry;

    private ReservationManager $reservationManager;

    public function __construct(Registry $registry, ReservationManager $reservationManager)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PartReserved::class => 'inPartReserved',
        ];
    }

    public function inPartReserved(GenericEvent $event): void
    {
        $reservation = $event->getSubject();
        if (!$reservation instanceof Reservation) {
            throw new LogicException('Reservation required.');
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

        $em = $this->registry->manager(Order::class);

        $order->setStatus(OrderStatus::notification());
        $em->flush();
    }
}
