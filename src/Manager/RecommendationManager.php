<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\EntityManager;
use App\Entity\Landlord\CarRecommendation;
use App\Entity\Landlord\CarRecommendationPart;
use App\Entity\Landlord\User;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RecommendationManager
{
    use EntityManager;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(RegistryInterface $registry, ReservationManager $reservationManager)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
    }

    public function realize(CarRecommendation $recommendation, Order $order, User $user): void
    {
        $em = $this->registry->getManager('default');

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
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
            }
        }
    }

    public function recommend(OrderItemService $orderItemService): void
    {
        $em = $this->getManager();
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

            $em->remove($orderItemService);
            $em->persist($recommendation);
        });
    }

    public function findOldRecommendation(OrderItemService $orderItemService): ?CarRecommendation
    {
        $em = $this->getManager();

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
            yield from $this->getParts($child);
        }
    }
}
