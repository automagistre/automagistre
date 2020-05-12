<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Event\OrderClosed;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Part\Domain\PartCase;
use App\Vehicle\Domain\VehicleId;
use function array_map;
use function count;
use Doctrine\DBAL\Connection;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartCaseOnOrderCloseListener implements EventSubscriberInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderClosed::class => 'onOrderClosed',
        ];
    }

    public function onOrderClosed(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof Order) {
            throw new LogicException('Order expected.');
        }

        $car = $order->getCar();
        if (null === $car) {
            return;
        }

        $vehicleId = $car->vehicleId;
        if (!$vehicleId instanceof VehicleId) {
            return;
        }

        $vehicleView = $this->registry->view($vehicleId);
        if (null === $vehicleView['caseName']) {
            return;
        }

        $parts = $order->getItems(OrderItemPart::class);
        if (0 === count($parts)) {
            return;
        }

        $parts = array_map(fn (OrderItemPart $orderItemPart) => $orderItemPart->getPart()->toId()->toString(), $parts);

        $this->registry->connection(PartCase::class)
            ->executeUpdate(
                'INSERT INTO part_case (part_id, vehicle_id)
                    SELECT part_id, :vehicle
                    FROM part 
                    WHERE universal IS FALSE 
                    AND part_id IN (:parts) 
                ON CONFLICT DO NOTHING
                ',
                [
                    'vehicle' => $vehicleId->toString(),
                    'parts' => $parts,
                ],
                [
                    'parts' => Connection::PARAM_STR_ARRAY,
                ]
            );
    }
}
