<?php

declare(strict_types=1);

namespace App\Part\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Messages\OrderClosed;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartCaseId;
use App\Shared\Doctrine\Registry;
use App\Vehicle\Entity\VehicleId;
use function array_map;
use function count;

final class LinkPartCaseOnOrderClosedListener implements MessageHandler
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->registry->get(Order::class, $event->orderId);

        $carId = $order->getCarId();
        if (null === $carId) {
            return;
        }

        $carView = $this->registry->view($carId);
        $vehicleId = $carView['vehicleId'];
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

        $parts = array_map(fn (OrderItemPart $orderItemPart) => $orderItemPart->getPartId(), $parts);

        foreach ($parts as $part) {
            $this->registry->connection(PartCase::class)
                ->executeStatement(
                    'INSERT INTO part_case (id, part_id, vehicle_id)
                        SELECT :id, id, :vehicle
                        FROM part 
                        WHERE universal IS FALSE 
                        AND id = :part
                    ON CONFLICT DO NOTHING
                    ',
                    [
                        'id' => PartCaseId::generate(),
                        'vehicle' => $vehicleId->toString(),
                        'part' => $part,
                    ]
                );
        }
    }
}
