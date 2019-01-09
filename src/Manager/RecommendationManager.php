<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Entity\Landlord\User;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\Reservation;
use App\Request\State;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    /**
     * @var State
     */
    private $state;

    public function __construct(RegistryInterface $registry, ReservationManager $reservationManager, State $state)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
        $this->state = $state;
    }

    public function realize(CarRecommendation $recommendation, Order $order, User $user): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(OrderItemService::class);

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
        $this->registry->getEntityManagerForClass(\get_class($recommendation))->flush();

        foreach ($orderItemParts as $orderItemPart) {
            try {
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
            }
        }
    }

    public function recommend(OrderItemService $orderItemService): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(CarRecommendation::class);
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

                try {
                    $this->reservationManager->deReserve($orderItemPart);
                } catch (ReservationException $e) {
                }
            }

            $em->persist($recommendation);
        });

        $em = $this->registry->getEntityManagerForClass(OrderItemService::class);
        $em->remove($orderItemService);
        $em->flush();
    }

    public function findOldRecommendation(OrderItemService $orderItemService): ?CarRecommendation
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(CarRecommendation::class);

        return $em->createQueryBuilder()
            ->select('entity')
            ->from(CarRecommendation::class, 'entity')
            ->where('entity.realization.uuid = :realization')
            ->orderBy('entity.id', 'DESC')
            ->getQuery()
            ->setParameter('realization', $orderItemService->uuid(), 'uuid_binary')
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
            yield from $this->getParts($child);
        }
    }
}
