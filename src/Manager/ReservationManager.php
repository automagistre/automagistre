<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItemPart;
use App\Entity\Part;
use App\Entity\Reservation;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReservationManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var PartManager
     */
    private $partManager;

    public function __construct(RegistryInterface $registry, PartManager $partManager)
    {
        $this->registry = $registry;
        $this->partManager = $partManager;
    }

    public function reserve(OrderItemPart $orderItemPart, int $quantity): void
    {
        if (0 >= $quantity) {
            throw new ReservationException('Количество резервируемого товара должно быть положительным.');
        }

        $part = $orderItemPart->getPart();

        $reservable = $this->reservable($part);
        if ($reservable < $quantity) {
            throw new ReservationException(
                \sprintf(
                    'Невозможно зарезервировать "%s" единиц товара, доступно "%s"',
                    $quantity / 100,
                    $reservable / 100
                )
            );
        }

        $em = $this->registry->getEntityManager();

        $em->persist(new Reservation($orderItemPart, $quantity));
        $em->flush();
    }

    public function deReserve(OrderItemPart $orderItemPart, int $quantity): void
    {
        if (0 >= $quantity) {
            throw new ReservationException('Количество снимаемого с резерва товара должно быть положительным.');
        }

        $reserved = $this->reserved($orderItemPart);
        if ($reserved < $quantity) {
            throw new ReservationException(
                \sprintf(
                    'Невозможно снять с резервации "%s" единиц товара, в резерве "%s"',
                    $quantity / 100,
                    $reserved / 100
                )
            );
        }

        $em = $this->registry->getEntityManager();

        $em->persist(new Reservation($orderItemPart, 0 - $quantity));
        $em->flush();
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
        $em = $this->registry->getEntityManager();

        [$part, $orderItemPart] = $part instanceof OrderItemPart ? [$part->getPart(), $part] : [$part, null];

        $qb = $em->createQueryBuilder()
            ->select('SUM(reservation.quantity)')
            ->from(Reservation::class, 'reservation')
            ->join('reservation.orderItemPart', 'order_item_part')
            ->groupBy('order_item_part.part')
            ->where('order_item_part.part = :part')
            ->setParameter('part', $part);

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
     * @return OrderItemPart[]
     */
    public function orders(Part $part): array
    {
        $em = $this->registry->getEntityManager();

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->join(Reservation::class, 'reservation', Join::WITH, 'reservation.orderItemPart = order_item_part')
            ->where('order_item_part.part = :part')
            ->orderBy('entity.id', 'DESC')
            ->setParameters([
                'part' => $part,
            ])
            ->getQuery()
            ->getResult();
    }
}
