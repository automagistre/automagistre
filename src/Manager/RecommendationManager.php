<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\Reservation;
use App\State;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationManager
{
    private Registry $registry;

    private ReservationManager $reservationManager;

    private State $state;

    public function __construct(Registry $registry, ReservationManager $reservationManager, State $state)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
        $this->state = $state;
    }

    public function realize(CarRecommendation $recommendation, Order $order, User $user): void
    {
        $em = $this->registry->manager(OrderItemService::class);

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

        $em->persist($orderItemService);
        $em->flush();

        $recommendation->realize($orderItemService, $this->state->tenant());
        $this->registry->manager($recommendation)->flush();

        foreach ($orderItemParts as $orderItemPart) {
            try {
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
            }
        }
    }

    public function recommend(OrderItemService $orderItemService): void
    {
        $em = $this->registry->manager(CarRecommendation::class);
        $order = $orderItemService->getOrder();

        if (null === $car = $order->getCar()) {
            throw new DomainException('Can\' recommend service on undefined car');
        }

        $em->transactional(function (EntityManagerInterface $em) use ($orderItemService, $car): void {
            $oldRecommendation = $this->findOldRecommendation($orderItemService);

            [$worker, $createdBy] = $oldRecommendation instanceof CarRecommendation
                ? [$oldRecommendation->getWorker(), $oldRecommendation->getCreatedBy()]
                : [$orderItemService->getWorker(), $orderItemService->getCreatedBy()];

            $recommendation = new CarRecommendation(
                $car,
                $orderItemService->getService(),
                $orderItemService->getPrice(),
                $worker,
                $createdBy
            );
            if ($oldRecommendation instanceof CarRecommendation) {
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

                $recommendation->addPart(new CarRecommendationPart(
                    $recommendation,
                    $orderItemPart->getPart(),
                    $orderItemPart->getQuantity(),
                    $orderItemPart->getPrice(),
                    $orderItemPart->getCreatedBy()
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

    public function findOldRecommendation(OrderItemService $orderItemService): ?CarRecommendation
    {
        $em = $this->registry->manager(CarRecommendation::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(CarRecommendation::class, 'entity')
            ->where('entity.realization.id = :realization')
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
