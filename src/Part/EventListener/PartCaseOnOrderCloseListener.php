<?php

declare(strict_types=1);

namespace App\Part\EventListener;

use App\Order\Entity\OrderItemPart;
use App\Order\Event\OrderClosed;
use App\Part\Entity\PartCase;
use App\Part\Entity\PartCaseId;
use App\Shared\Doctrine\Registry;
use App\Vehicle\Entity\VehicleId;
use function array_map;
use function count;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    public function onOrderClosed(OrderClosed $event): void
    {
        $order = $event->getSubject();

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
