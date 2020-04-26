<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
use App\Event\OrderClosed;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
use App\Vehicle\Domain\Model;
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

        $carModel = $car->model;
        if (!$carModel instanceof Model) {
            return;
        }

        if (null === $carModel->caseName) {
            return;
        }

        $parts = $order->getItems(OrderItemPart::class);
        if (0 === count($parts)) {
            return;
        }

        /** @var Part[] $parts */
        $parts = array_map(fn (OrderItemPart $orderItemPart) => $orderItemPart->getPart(), $parts);

        $em = $this->registry->manager(PartCase::class);

        $existed = $this->registry->repository(Part::class)->createQueryBuilder('entity')
            ->select('entity.id')
            ->join(PartCase::class, 'pc', Join::WITH, 'entity = pc.part')
            ->where('pc.part IN (:parts)')
            ->andWhere('pc.carModel = :carModel')
            ->getQuery()
            ->setParameter('carModel', $carModel)
            ->setParameter('parts', $parts)
            ->getScalarResult();
        $existed = array_map('array_shift', $existed);
        $existed = array_flip($existed);

        foreach ($parts as $part) {
            if (array_key_exists($part->getId(), $existed)) {
                continue;
            }

            if ($part->isUniversal()) {
                continue;
            }

            $em->persist(new PartCase($part, $carModel));
        }

        $em->flush();
    }
}
