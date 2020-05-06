<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
use App\Event\OrderClosed;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
use App\Vehicle\Domain\VehicleId;
use function array_flip;
use function array_key_exists;
use function array_map;
use function count;
use Doctrine\ORM\Query\Expr\Join;
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

        /** @var Part[] $parts */
        $parts = array_map(fn (OrderItemPart $orderItemPart) => $orderItemPart->getPart(), $parts);

        $em = $this->registry->manager(PartCase::class);

        $existed = $this->registry->repository(Part::class)
            ->createQueryBuilder('entity')
            ->select('entity.partId')
            ->join(PartCase::class, 'pc', Join::WITH, 'entity.partId = pc.partId')
            ->where('pc.partId IN (:parts)')
            ->andWhere('pc.vehicleId = :vehicle')
            ->getQuery()
            ->setParameter('vehicle', $vehicleId)
            ->setParameter('parts', array_map(fn (Part $part) => $part->toId(), $parts))
            ->getScalarResult();
        $existed = array_map('array_shift', $existed);
        $existed = array_map(fn (Identifier $identifier) => $identifier->toString(), $existed);
        $existed = array_flip($existed);

        foreach ($parts as $part) {
            if (array_key_exists($part->getId(), $existed)) {
                continue;
            }

            if ($part->universal) {
                continue;
            }

            $em->persist(new PartCase($part->toId(), $vehicleId));
        }

        $em->flush();
    }
}
