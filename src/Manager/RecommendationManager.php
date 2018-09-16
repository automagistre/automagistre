<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\CarRecommendation;
use App\Entity\CarRecommendationPart;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Entity\OrderItemService;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Generator;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(
        EntityManager $em,
        TokenStorageInterface $tokenStorage,
        ReservationManager $reservationManager
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->reservationManager = $reservationManager;
    }

    public function realize(CarRecommendation $recommendation, Order $order): void
    {
        $em = $this->em;

        $orderService = new OrderItemService($order, $recommendation->getService(), $recommendation->getPrice());

        $orderItemParts = [];
        foreach ($recommendation->getParts() as $recommendationPart) {
            $orderItemPart = $orderItemParts[] = new OrderItemPart(
                $order,
                $recommendationPart->getPart(),
                $recommendationPart->getQuantity(),
                $recommendationPart->getPrice(),
                $recommendationPart->getSelector()
            );

            $orderItemPart->setParent($orderService);
            $em->persist($orderItemPart);
        }

        $recommendation->realize($order);

        $em->persist($orderService);
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
            $recommendation = new CarRecommendation(
                $car,
                $orderItemService->getService(),
                $orderItemService->getPrice(),
                $this->getUser()->getPerson()
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

    private function getUser(): User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new DomainException('Recommendation manager cannot work with anonymous user');
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new LogicException(\sprintf('User must be instance of "%s"', User::class));
        }

        return $user;
    }
}
