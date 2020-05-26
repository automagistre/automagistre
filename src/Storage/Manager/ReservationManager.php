<?php

declare(strict_types=1);

namespace App\Storage\Manager;

use App\Entity\Tenant\Reservation;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Part\Entity\PartId;
use App\Part\Event\PartDeReserved;
use App\Part\Event\PartReserved;
use App\Part\Manager\PartManager;
use App\Shared\Doctrine\Registry;
use App\Storage\Exception\ReservationException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use function sprintf;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReservationManager
{
    private Registry $registry;

    private PartManager $partManager;

    private EventDispatcherInterface $dispatcher;

    public function __construct(Registry $registry, PartManager $partManager, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->partManager = $partManager;
        $this->dispatcher = $dispatcher;
    }

    public function reserve(OrderItemPart $orderItemPart, ?int $quantity = null): void
    {
        $quantity ??= $orderItemPart->getQuantity();

        if (0 >= $quantity) {
            throw new ReservationException('Количество резервируемого товара должно быть положительным.');
        }

        $partId = $orderItemPart->getPartId();

        $reserved = $this->reserved($orderItemPart);
        if (0 < $reserved) {
            $this->deReserve($orderItemPart, $reserved);
        }

        $reservable = $this->reservable($partId);
        if ($reservable < $quantity) {
            throw new ReservationException(
                sprintf(
                    'Невозможно зарезервировать "%s" единиц товара, доступно "%s"',
                    $quantity / 100,
                    $reservable / 100
                )
            );
        }

        $reservation = new Reservation($orderItemPart, $quantity);

        $em = $this->registry->manager(Reservation::class);
        $em->persist($reservation);
        $em->flush();

        $this->dispatcher->dispatch(new PartReserved($reservation));
    }

    public function deReserve(OrderItemPart $orderItemPart, int $quantity = null): void
    {
        $quantity ??= $orderItemPart->getQuantity();

        if (0 >= $quantity) {
            throw new ReservationException('Количество снимаемого с резерва товара должно быть положительным.');
        }

        $reserved = $this->reserved($orderItemPart);
        if ($reserved < $quantity) {
            throw new ReservationException(
                sprintf(
                    'Невозможно снять с резервации "%s" единиц товара, в резерве "%s"',
                    $quantity / 100,
                    $reserved / 100
                )
            );
        }

        $em = $this->registry->manager(Reservation::class);

        $reservation = new Reservation($orderItemPart, 0 - $quantity);
        $em->persist($reservation);
        $em->flush();

        $this->dispatcher->dispatch(new PartDeReserved($reservation));
    }

    public function reservable(PartId $part): int
    {
        return $this->partManager->inStock($part) - $this->reserved($part);
    }

    /**
     * @param PartId|OrderItemPart $part
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function reserved($part): int
    {
        $em = $this->registry->manager(Reservation::class);

        [$partId, $orderItemPart] = $part instanceof OrderItemPart ? [$part->getPartId(), $part] : [$part, null];

        $qb = $em->createQueryBuilder()
            ->select('SUM(reservation.quantity)')
            ->from(Reservation::class, 'reservation')
            ->join('reservation.orderItemPart', 'order_item_part')
            ->groupBy('order_item_part.partId')
            ->where('order_item_part.partId = :part')
            ->setParameter('part', $partId);

        if (null !== $orderItemPart) {
            $qb->andWhere('reservation.orderItemPart = :orderItemPart')
                ->setParameter('orderItemPart', $orderItemPart);
        }

        try {
            return (int) $qb
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @return Order[]
     */
    public function orders(PartId $partId): array
    {
        $em = $this->registry->manager(Order::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->join(Reservation::class, 'reservation', Join::WITH, 'reservation.orderItemPart = order_item_part')
            ->where('order_item_part.partId = :part')
            ->orderBy('entity.id', 'DESC')
            ->setParameter('part', $partId)
            ->getQuery()
            ->getResult();
    }
}
