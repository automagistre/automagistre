<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\CarRecommendation;
use App\Entity\CarRecommendationPart;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Entity\OrderItemService;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(EntityManager $em, ReservationManager $reservationManager)
    {
        $this->em = $em;
        $this->reservationManager = $reservationManager;
    }

    public function realize(CarRecommendation $recommendation, Order $order, User $user): void
    {
        $em = $this->em;

        $orderItemService = new OrderItemService(
            $order,
            $recommendation->getService(),
            $recommendation->getPrice(),
            $user,
            $order->getActiveWorker()
        );

        $orderItemParts = [];
        foreach ($recommendation->getParts() as $recommendationPart) {
            $orderItemPart = $orderItemParts[] = new OrderItemPart(
                $order,
                $recommendationPart->getPart(),
                $recommendationPart->getQuantity(),
                $recommendationPart->getPrice(),
                $recommendationPart->getCreatedBy()
            );

            $orderItemPart->setParent($orderItemService);
            $em->persist($orderItemPart);
        }

        $recommendation->realize($orderItemService);

        $em->persist($orderItemService);
        $em->flush();

        foreach ($orderItemParts as $orderItemPart) {
            try {
                $this->reservationManager->reserve($orderItemPart, $orderItemPart->getQuantity());
            } catch (ReservationException $e) {
            }
        }
    }

    public function recommend(OrderItemService $orderItemService): void
    {
        $order = $orderItemService->getOrder();

        if (null === $car = $order->getCar()) {
            throw new DomainException('Can\' recommend service on undefined car');
        }

        $this->em->transactional(function (EntityManagerInterface $em) use ($orderItemService, $car): void {
            $oldRecommendation = $this->findOldRecommendation($orderItemService);

            $recommendation = new CarRecommendation(
                $car,
                $orderItemService->getService(),
                $orderItemService->getPrice(),
                $oldRecommendation instanceof CarRecommendation
                    ? $oldRecommendation->getWorker()
                    : $orderItemService->getWorker()
            );

            foreach ($oldRecommendation->getParts() as $part) {
                $em->remove($part);
            }
            $em->remove($oldRecommendation);

            foreach ($this->getParts($orderItemService) as $orderItemPart) {
                $em->createQueryBuilder()
                    ->delete()
                    ->from(Reservation::class, 'entity')
                    ->where('entity.orderItemPart = :item')
                    ->getQuery()
                    ->setParameters([
                        'item' => $orderItemPart,
                    ])
                    ->execute();

                $recommendation->addPart(new CarRecommendationPart(
                    $recommendation,
                    $orderItemPart->getPart(),
                    $orderItemPart->getQuantity(),
                    $orderItemPart->getPrice(),
                    $orderItemPart->getCreatedBy()
                ));
            }

            $em->remove($orderItemService);
            $em->persist($recommendation);
        });
    }

    public function findOldRecommendation(OrderItemService $orderItemService): ?CarRecommendation
    {
        $em = $this->em;

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(CarRecommendation::class, 'entity')
            ->where('entity.realization = :realization')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setParameters([
                'realization' => $orderItemService,
            ])
            ->getOneOrNullResult();
    }

    /**
     * @return OrderItemPart[]|Generator
     */
    private function getParts(OrderItem $item): Generator
    {
        if ($item instanceof OrderItemPart) {
            yield $item;
        }

        foreach ($item->getChildren() as $child) {
            foreach ($this->getParts($child) as $part) {
                yield $part;
            }
        }
    }
}
