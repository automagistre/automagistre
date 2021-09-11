<?php

declare(strict_types=1);

namespace App\Part\Manager;

use App\Doctrine\Registry;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Enum\OrderStatus;
use App\Part\Entity\Part;
use App\Part\Entity\PartCross;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Storage\Entity\Motion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    public function __construct(private Registry $registry)
    {
    }

    public function byId(PartId $partId): Part
    {
        return $this->registry->getBy(Part::class, ['id' => $partId]);
    }

    public function inStock(PartId $partId): int
    {
        $em = $this->registry->manager(Motion::class);

        try {
            return (int) $em->createQueryBuilder()
                ->select('SUM(entity.quantity)')
                ->from(Motion::class, 'entity')
                ->groupBy('entity.part')
                ->where('entity.part = :part')
                ->setParameter('part', $partId)
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR)
            ;
        } catch (NoResultException) {
            return 0;
        }
    }

    public function inOrders(PartId $partId): array
    {
        $em = $this->registry->manager(Order::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->where('order_item_part.partId = :part')
            ->andWhere('entity.status NOT IN (:statuses)')
            ->orderBy('entity.id', 'DESC')
            ->setParameter('part', $partId)
            ->setParameter('statuses', [OrderStatus::closed(), OrderStatus::cancelled()])
            ->getQuery()
            ->getResult()
        ;
    }

    public function cross(PartId $left, PartId $right): void
    {
        $em = $this->registry->manager(Part::class);

        $em->transactional(function (EntityManagerInterface $em) use ($left, $right): void {
            $leftGroup = $this->registry->findOneBy(PartCross::class, ['partId' => $left]);
            $rightGroup = $this->registry->findOneBy(PartCross::class, ['partId' => $right]);

            if (null === $leftGroup && null === $rightGroup) {
                $uuid = Uuid::uuid6();
                $em->persist(new PartCross($uuid, $left));
                $em->persist(new PartCross($uuid, $right));
            } elseif (null === $leftGroup) {
                $em->persist(new PartCross($rightGroup->id, $left));
            } elseif (null === $rightGroup) {
                $em->persist(new PartCross($leftGroup->id, $right));
            } else {
                $em->getConnection()->executeQuery('UPDATE part_cross_part SET part_cross_id = :newId where part_cross_id = :oldId', [
                    'newId' => $leftGroup->id->toString(),
                    'oldId' => $rightGroup->id->toString(),
                ]);
            }
        });
    }

    public function uncross(PartId $partId): void
    {
        $this->registry->connection(Part::class)
            ->executeQuery('DELETE FROM part_cross_part WHERE part_cross_part.part_id = :partId', [
                'partId' => $partId->toString(),
            ])
        ;
    }

    /**
     * @return array<string, PartView>
     */
    public function crossesInStock(PartId $partId): array
    {
        /** @var PartView $partView */
        $partView = $this->registry->getBy(PartView::class, ['id' => $partId]);

        return $this->registry->manager(PartView::class)
            ->createQueryBuilder()
            ->select('t')
            ->from(PartView::class, 't', 't.id')
            ->where('t.id IN (:ids)')
            ->andWhere('t.id <> :id')
            ->andWhere('t.quantity > 0')
            ->getQuery()
            ->setParameter('id', $partId)
            ->setParameter('ids', $partView->analogs)
            ->getResult()
        ;
    }
}
