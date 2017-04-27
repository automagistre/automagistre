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
use App\Exception\DomainException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function realize(CarRecommendation $recommendation, Order $order): void
    {
        $orderService = new OrderItemService($order, $recommendation->getService(), $recommendation->getPrice());

        foreach ($recommendation->getParts() as $recommendationPart) {
            $orderPart = new OrderItemPart(
                $order,
                $recommendationPart->getSelector(),
                $recommendationPart->getPart(),
                $recommendationPart->getQuantity(),
                $recommendationPart->getPrice()
            );

            $orderPart->setParent($orderService);
            $this->em->persist($orderPart);
        }

        $recommendation->realize($order);

        $this->em->persist($orderService);
        $this->em->flush();
    }

    public function recommend(OrderItemService $orderService): void
    {
        $order = $orderService->getOrder();

        if (!$car = $order->getCar()) {
            throw new DomainException('Can\' recommend service on undefined car');
        }

        $recommendation = new CarRecommendation(
            $car,
            $orderService->getService(),
            $orderService->getPrice(),
            $this->getUser()->getPerson()
        );

        foreach ($this->getParts($orderService) as $orderPart) {
            $recommendation->addPart(new CarRecommendationPart(
                $recommendation,
                $orderPart->getSelector(),
                $orderPart->getPart(),
                $orderPart->getQuantity(),
                $orderPart->getPrice()
            ));
        }

        $this->em->remove($orderService);
        $this->em->persist($recommendation);
        $this->em->flush();
    }

    /**
     * @param OrderItem $item
     *
     * @return OrderItemPart[]
     */
    private function getParts(OrderItem $item): array
    {
        $parts = [];

        if ($item instanceof OrderItemPart) {
            $parts[] = $item;
        }

        $nested = [];
        foreach ($item->getChildren() as $child) {
            $nested[] = $this->getParts($child);
        }

        if (count($nested)) {
            $nested = array_merge(...$nested);
        }

        return array_merge($parts, $nested);
    }

    private function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
