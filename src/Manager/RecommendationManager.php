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

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage)
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

        if (null === $car = $order->getCar()) {
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
            throw new LogicException(sprintf('User must be instance of "%s"', User::class));
        }

        return $user;
    }
}
