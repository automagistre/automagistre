<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\Reservation;
use App\Event\PartDeReserved;
use App\Event\PartReserved;
use App\Part\Domain\Part;
use Doctrine\ORM\EntityManagerInterface;
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

        $part = $orderItemPart->getPart();

        $reserved = $this->reserved($orderItemPart);
        if (0 < $reserved) {
            $this->deReserve($orderItemPart, $reserved);
        }

        $reservable = $this->reservable($part);
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

        /** @var EntityManagerInterface $em */
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

        /** @var EntityManagerInterface $em */
        $em = $this->registry->manager(Reservation::class);

        $reservation = new Reservation($orderItemPart, 0 - $quantity);
        $em->persist($reservation);
        $em->flush();

        $this->dispatcher->dispatch(new PartDeReserved($reservation));
    }

    public function reservable(Part $part): int
    {
        return $this->partManager->inStock($part) - $this->reserved($part);
    }

    /**
     * @param Part|OrderItemPart $part
     */
    public function reserved($part): int
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->manager(Reservation::class);

        [$part, $orderItemPart] = $part instanceof OrderItemPart ? [$part->getPart(), $part] : [$part, null];
        /** @var Part $part */
        $qb = $em->createQueryBuilder()
            ->select('SUM(reservation.quantity)')
            ->from(Reservation::class, 'reservation')
            ->join('reservation.orderItemPart', 'order_item_part')
            ->groupBy('order_item_part.part.id')
            ->where('order_item_part.part.id = :part')
            ->setParameter('part', $part->getId());

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
    public function orders(Part $part): array
    {
        $em = $this->registry->manager(Order::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->join(Reservation::class, 'reservation', Join::WITH, 'reservation.orderItemPart = order_item_part')
            ->where('order_item_part.part.id = :part')
            ->orderBy('entity.id', 'DESC')
            ->setParameter('part', $part->getId())
            ->getQuery()
            ->getResult();
    }
}
