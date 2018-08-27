<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Motion;
use App\Entity\Order;
use App\Entity\OrderItemPart;
use App\Entity\Part;
use App\Enum\OrderStatus;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function inStock(Part $part): int
    {
        $em = $this->registry->getEntityManager();

        return (int) $em->createQueryBuilder()
            ->select('SUM(entity.quantity)')
            ->from(Motion::class, 'entity')
            ->groupBy('entity.part')
            ->where('entity.part = :part')
            ->setParameter('part', $part)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function inOrders(Part $part): array
    {
        $em = $this->registry->getEntityManager();

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->where('order_item_part.part = :part')
            ->andWhere('entity.status NOT IN (:statuses)')
            ->orderBy('entity.id', 'DESC')
            ->setParameters([
                'part' => $part,
                'statuses' => OrderStatus::closed(),
            ])
            ->getQuery()
            ->getResult();
    }
}
