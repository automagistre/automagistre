<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Motion;
use App\Entity\Order;
use App\Entity\OrderItemPart;
use App\Entity\Part;
use App\Entity\PartCross;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
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

        try {
            return (int) $em->createQueryBuilder()
                ->select('SUM(entity.quantity)')
                ->from(Motion::class, 'entity')
                ->groupBy('entity.part')
                ->where('entity.part = :part')
                ->setParameter('part', $part)
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return 0;
        }
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

    public function cross(Part $left, Part $right): void
    {
        $em = $this->registry->getEntityManager();

        $em->transactional(function (EntityManagerInterface $em) use ($left, $right): void {
            $leftGroup = $this->findCross($em, $left);
            $rightGroup = $this->findCross($em, $right);

            if (null === $leftGroup && null === $rightGroup) {
                $em->persist(new PartCross($left, $right));
            } elseif (null === $leftGroup && null !== $rightGroup) {
                $rightGroup->addPart($left);
            } elseif (null !== $leftGroup && null === $rightGroup) {
                $leftGroup->addPart($right);
            } elseif (null !== $leftGroup && null !== $rightGroup) {
                $parts = $rightGroup->getParts();
                $em->remove($rightGroup);
                $em->flush();
                $leftGroup->addPart(...$parts);
            }
        });
    }

    public function uncross(Part $part): void
    {
        $em = $this->registry->getEntityManager();
        $cross = $this->findCross($em, $part);
        $cross->removePart($part);

        if ($cross->isEmpty()) {
            $em->remove($cross);
        }

        $em->flush();
    }

    /**
     * @return Part[]
     */
    public function getCrosses(Part $part): ?array
    {
        $cross = $this->findCross($this->registry->getEntityManager(), $part);
        if (!$cross instanceof PartCross) {
            return null;
        }

        return $cross->getParts();
    }

    private function findCross(EntityManagerInterface $em, Part $part): ?PartCross
    {
        return $em->createQueryBuilder()
            ->select('entity')
            ->from(PartCross::class, 'entity')
            ->where(':part MEMBER OF entity.parts')
            ->getQuery()
            ->setParameters([
                'part' => $part,
            ])
            ->getOneOrNullResult();
    }
}
