<?php

declare(strict_types=1);

namespace App\Car\Manager;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Entity\RecommendationPart;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\Reservation;
use App\Order\Exception\ReservationException;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\PartView;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;
use function get_class;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationManager
{
    private Registry $registry;

    private ReservationManager $reservationManager;

    public function __construct(Registry $registry, ReservationManager $reservationManager)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
    }

    public function realize(Recommendation $recommendation, Order $order): void
    {
        $em = $this->registry->manager(OrderItemService::class);

        $orderItemService = new OrderItemService(
            $order,
            $recommendation->service,
            $recommendation->getPrice(),
            $order->getWorkerPersonId(),
        );

        $orderItemParts = [];
        foreach ($recommendation->getParts() as $recommendationPart) {
            $partId = $recommendationPart->partId;

            $orderItemPart = $orderItemParts[] = new OrderItemPart(
                $order,
                $partId,
                $recommendationPart->quantity,
            );

            $orderItemPart->setPrice(
                $recommendationPart->getPrice(),
                $this->registry->get(PartView::class, $partId)
            );
            $orderItemPart->setParent($orderItemService);
            $em->persist($orderItemPart);
        }

        $em->persist($orderItemService);
        $em->flush();

        $recommendation->realize($orderItemService);
        $this->registry->manager(get_class($recommendation))->flush();

        foreach ($orderItemParts as $orderItemPart) {
            try {
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
            }
        }
    }

    public function recommend(OrderItemService $orderItemService): void
    {
        $em = $this->registry->manager(Recommendation::class);
        $order = $orderItemService->getOrder();

        if (null === $carId = $order->getCarId()) {
            throw new DomainException('Can\' recommend service on undefined car');
        }

        /** @var Car $car */
        $car = $this->registry->findBy(Car::class, ['uuid' => $carId]);

        $em->transactional(function (EntityManagerInterface $em) use ($orderItemService, $car): void {
            $oldRecommendation = $this->findOldRecommendation($orderItemService);

            [$worker, $createdBy] = $oldRecommendation instanceof Recommendation
                ? [$oldRecommendation->workerId, $oldRecommendation->createdBy]
                : [$orderItemService->workerId, $orderItemService->getCreatedBy()->toId()];

            $recommendation = new Recommendation(
                $car,
                $orderItemService->service,
                $orderItemService->getPrice(),
                $worker,
                $createdBy
            );
            if ($oldRecommendation instanceof Recommendation) {
                foreach ($oldRecommendation->getParts() as $part) {
                    $em->remove($part);
                }
                $em->remove($oldRecommendation);
            }

            foreach ($this->getParts($orderItemService) as $orderItemPart) {
                $this->registry->manager(Reservation::class)->createQueryBuilder()
                    ->delete()
                    ->from(Reservation::class, 'entity')
                    ->where('entity.orderItemPart = :item')
                    ->getQuery()
                    ->setParameter('item', $orderItemPart)
                    ->execute();

                $recommendation->addPart(new RecommendationPart(
                    $recommendation,
                    $orderItemPart->getPartId(),
                    $orderItemPart->getQuantity(),
                    $orderItemPart->getPrice(),
                    $orderItemPart->getCreatedBy()->toId()
                ));

                try {
                    $this->reservationManager->deReserve($orderItemPart);
                } catch (ReservationException $e) {
                }
            }

            $em->persist($recommendation);
        });

        $em = $this->registry->manager(OrderItemService::class);
        $em->remove($orderItemService);
        $em->flush();
    }

    public function findOldRecommendation(OrderItemService $orderItemService): ?Recommendation
    {
        $em = $this->registry->manager(Recommendation::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(Recommendation::class, 'entity')
            ->where('entity.realization = :realization')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setParameter('realization', $orderItemService->getId())
            ->getOneOrNullResult();
    }

    /**
     * @return Generator<OrderItemPart>
     */
    private function getParts(OrderItem $item): Generator
    {
        if ($item instanceof OrderItemPart) {
            yield $item;
        }

        foreach ($item->getChildren() as $child) {
            yield from $this->getParts($child);
        }
    }
}
