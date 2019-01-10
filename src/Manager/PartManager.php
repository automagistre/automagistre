<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Landlord\Part;
use App\Entity\Landlord\PartCross;
use App\Entity\Tenant\Motion;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
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
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getEntityManagerForClass(Motion::class);

        try {
            return (int) $em->createQueryBuilder()
                ->select('SUM(entity.quantity)')
                ->from(Motion::class, 'entity')
                ->groupBy('entity.part.uuid')
                ->where('entity.part.uuid = :part')
                ->setParameter('part', $part->uuid(), 'uuid_binary')
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function inOrders(Part $part): array
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(Order::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->where('order_item_part.part.uuid = :part')
            ->andWhere('entity.status NOT IN (:statuses)')
            ->orderBy('entity.id', 'DESC')
            ->setParameter('part', $part->uuid(), 'uuid_binary')
            ->setParameter('statuses', OrderStatus::closed())
            ->getQuery()
            ->getResult();
    }

    public function cross(Part $left, Part $right): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(Part::class);

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
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(Part::class);

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
