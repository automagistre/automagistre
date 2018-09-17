<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\CarRecommendation;
use App\Entity\CarRecommendationPart;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Entity\OrderItemService;
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

    public function realize(CarRecommendation $recommendation, Order $order): void
    {
        $em = $this->em;

        $orderItemService = new OrderItemService(
            $order,
            $recommendation->getService(),
            $recommendation->getPrice(),
            $order->getActiveWorker()
        );

        $orderItemParts = [];
        foreach ($recommendation->getParts() as $recommendationPart) {
            $orderItemPart = $orderItemParts[] = new OrderItemPart(
                $order,
                $recommendationPart->getPart(),
                $recommendationPart->getQuantity(),
                $recommendationPart->getPrice(),
                $recommendationPart->getSelector()
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
            $oldRecommendation = $em->createQueryBuilder()
                ->select('entity')
                ->from(CarRecommendation::class, 'entity')
                ->where('entity.realization = :realization')
                ->orderBy('entity.id', 'DESC')
                ->getQuery()
                ->setParameters([
                    'realization' => $orderItemService,
                ])
                ->getOneOrNullResult();

            $recommendation = new CarRecommendation(
                $car,
                $orderItemService->getService(),
                $orderItemService->getPrice(),
                $oldRecommendation instanceof CarRecommendation
                    ? $oldRecommendation->getWorker()
                    : $orderItemService->getWorker()
            );

            foreach ($this->getParts($orderItemService) as $orderItemPart) {
                $part = $orderItemPart->getPart();
                $reserved = $this->reservationManager->reserved($orderItemPart);
                if (0 < $reserved) {
                    $this->reservationManager->deReserve($orderItemPart, $reserved);
                }

                $recommendation->addPart(new CarRecommendationPart(
                    $recommendation,
                    $part,
                    $orderItemPart->getQuantity(),
                    $orderItemPart->getPrice(),
                    $orderItemPart->getSelector()
                ));
            }

            $em->createQueryBuilder()
                ->delete()
                ->from(CarRecommendation::class, 'entity')
                ->where('entity.realization = :realization')
                ->setParameters([
                    'realization' => $orderItemService,
                ])
                ->getQuery()
                ->execute();

            $em->remove($orderItemService);
            $em->persist($recommendation);
        });
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
