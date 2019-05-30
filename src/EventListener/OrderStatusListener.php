<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\Reservation;
use App\Enum\OrderStatus;
use App\Event\PartReserved;
use App\Manager\ReservationManager;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatusListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

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

            $reservable = $this->reservationManager->reservable($item->getPart());
            if (($reserved + $reservable) < $required) {
                return;
            }
        }

        $em = $this->registry->manager($order);

        $order->setStatus(OrderStatus::notification());
        $em->flush();
    }
}
