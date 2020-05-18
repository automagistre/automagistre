<?php

declare(strict_types=1);

namespace App\Part\Manager;

use App\Income\Entity\IncomePart;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Enum\OrderStatus;
use App\Part\Domain\Part;
use App\Part\Domain\PartCross;
use App\Part\Domain\PartId;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\Motion;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    private const MARKUP = 1.15;

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function byId(PartId $partId): Part
    {
        return $this->registry->findBy(Part::class, ['partId' => $partId]);
    }

    public function price(PartId $partId): Money
    {
        /** @var Part $part */
        $part = $this->registry->findBy(Part::class, ['partId' => $partId]);

        return $part->price;
    }

    public function inStock(Part $part): int
    {
        $em = $this->registry->manager(Motion::class);

        try {
            return (int) $em->createQueryBuilder()
                ->select('SUM(entity.quantity)')
                ->from(Motion::class, 'entity')
                ->groupBy('entity.part.id')
                ->where('entity.part.id = :part')
                ->setParameter('part', $part->getId())
                ->getQuery()
                ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function inOrders(Part $part): array
    {
        $em = $this->registry->manager(Order::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Order::class, 'entity')
            ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'order_item_part.order = entity')
            ->where('order_item_part.part.id = :part')
            ->andWhere('entity.status NOT IN (:statuses)')
            ->orderBy('entity.id', 'DESC')
            ->setParameter('part', $part->getId())
            ->setParameter('statuses', OrderStatus::closed())
            ->getQuery()
            ->getResult();
    }

    public function cross(PartId $leftId, PartId $rightId): void
    {
        $em = $this->registry->manager(Part::class);
        $left = $this->byId($leftId);
        $right = $this->byId($rightId);

        $em->transactional(function (EntityManagerInterface $em) use ($left, $right): void {
            $leftGroup = $this->findCross($left, $em);
            $rightGroup = $this->findCross($right, $em);

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
        $em = $this->registry->manager(Part::class);

        $cross = $this->findCross($part, $em);
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
    public function getCrosses(Part $part): array
    {
        $cross = $this->findCross($part);
        if (!$cross instanceof PartCross) {
            return [];
        }

        return $cross->getParts();
    }

    /**
     * @return array<int, Part>
     */
    public function crossesInStock(Part $part): array
    {
        $crosses = [];
        foreach ($this->getCrosses($part) as $cross) {
            if ($part->equals($cross)) {
                continue;
            }

            if (0 < $this->inStock($cross)) {
                $crosses[] = $cross;
            }
        }

        return $crosses;
    }

    public function suggestPrice(Part $part): Money
    {
        $em = $this->registry->manager(IncomePart::class);
        $suggestPrice = $part->price;

        $incomePart = $em->createQueryBuilder()
            ->select('entity')
            ->from(IncomePart::class, 'entity')
            ->where('entity.partId = :part')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameter('part', $part->toId())
            ->getOneOrNullResult();

        if ($incomePart instanceof IncomePart) {
            $incomePriceWithMarkup = $incomePart->getPrice()->multiply(self::MARKUP);

            if ($incomePriceWithMarkup->greaterThan($suggestPrice)) {
                $suggestPrice = $incomePriceWithMarkup;
            }
        }

        return $suggestPrice;
    }

    private function findCross(Part $part, EntityManagerInterface $em = null): ?PartCross
    {
        $em = $em instanceof EntityManagerInterface ? $em : $this->registry->manager(PartCross::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(PartCross::class, 'entity')
            ->where(':part MEMBER OF entity.parts')
            ->getQuery()
            ->setParameter('part', $part)
            ->getOneOrNullResult();
    }
}
