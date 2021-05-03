<?php

declare(strict_types=1);

namespace App\Part\Manager;

use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Enum\OrderStatus;
use App\Part\Entity\Part;
use App\Part\Entity\PartCross;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\Motion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use function assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
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

    public function cross(PartId $leftId, PartId $rightId): void
    {
        $em = $this->registry->manager(Part::class);
        $left = $this->byId($leftId);
        $right = $this->byId($rightId);

        $em->transactional(function (EntityManagerInterface $em) use ($left, $right): void {
            $leftGroup = $this->findCross($left->toId());
            $rightGroup = $this->findCross($right->toId());

            if (null === $leftGroup && null === $rightGroup) {
                $em->persist(new PartCross($left, $right));
            } elseif (null === $leftGroup) {
                $rightGroup->addPart($left);
            } elseif (null === $rightGroup) {
                $leftGroup->addPart($right);
            } else {
                $parts = $rightGroup->getParts();
                $em->remove($rightGroup);
                $em->flush();
                $leftGroup->addPart(...$parts);
            }
        });
    }

    public function uncross(PartId $partId): void
    {
        $em = $this->registry->manager(Part::class);

        $part = $this->byId($partId);
        $cross = $this->findCross($partId);
        assert($cross instanceof PartCross);

        $cross->removePart($part);

        if ($cross->isEmpty()) {
            $em->remove($cross);
        }

        $em->flush();
    }

    /**
     * @return Part[]
     */
    public function getCrosses(PartId $partId): array
    {
        $cross = $this->findCross($partId);

        if (!$cross instanceof PartCross) {
            return [];
        }

        return $cross->getParts();
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

    private function findCross(PartId $partId): ?PartCross
    {
        $part = $this->byId($partId);

        return $this->registry->manager(PartCross::class)
            ->createQueryBuilder()
            ->select('entity')
            ->from(PartCross::class, 'entity')
            ->where(':part MEMBER OF entity.parts')
            ->getQuery()
            ->setParameter('part', $part)
            ->getOneOrNullResult()
        ;
    }
}
