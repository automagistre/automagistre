<?php

declare(strict_types=1);

namespace App\Part\Messages;

use App\Car\Entity\Car;
use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Messages\OrderDealed;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartCaseId;
use App\Shared\Doctrine\Registry;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use function array_map;
use function count;

final class LinkPartCaseOnOrderClosedListener implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(OrderDealed $event): void
    {
        $order = $this->registry->get(Order::class, $event->orderId);

        $carId = $order->getCarId();

        if (null === $carId) {
            return;
        }

        $car = $this->registry->get(Car::class, $carId);
        $vehicleId = $car->vehicleId;

        if (!$vehicleId instanceof VehicleId) {
            return;
        }

        $vehicle = $this->registry->get(Model::class, $vehicleId);

        if (null === $vehicle->caseName) {
            return;
        }

        $parts = $order->getItems(OrderItemPart::class);

        if (0 === count($parts)) {
            return;
        }

        $parts = array_map(static fn (OrderItemPart $orderItemPart) => $orderItemPart->getPartId(), $parts);

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
                )
            ;
        }
    }
}
