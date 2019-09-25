<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarRecommendation;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\Reservation;
use App\Manager\RecommendationManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class OrderItemController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return \array_merge(parent::getSubscribedServices(), [
            RecommendationManager::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if ('autocomplete' === $actionName) {
            return parent::isActionAllowed($actionName);
        }

        if (\in_array($actionName, ['edit', 'delete'], true) && null !== $id = $this->request->get('id')) {
            /** @var Order $order */
            $order = $this->em->getRepository(OrderItem::class)->find($id)->getOrder();

            return $order->isEditable();
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            return parent::isActionAllowed($actionName);
        }

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        if (!$order->isEditable()) {
            return false;
        }

        return parent::isActionAllowed($actionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function removeEntity($entity): void
    {
        \assert($entity instanceof OrderItem);

        $this->em->transactional(function (EntityManagerInterface $em) use ($entity): void {
            $this->recursiveRemove($em, $entity);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        $parameters['order'] = $this->getEntity(Order::class);
        $parameters['car'] = $this->getEntity(Car::class);

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    private function recursiveRemove(EntityManagerInterface $em, OrderItem $item): void
    {
        if ($item instanceof OrderItemPart) {
            $em->createQueryBuilder()
                ->delete()
                ->from(Reservation::class, 'entity')
                ->where('entity.orderItemPart = :item')
                ->getQuery()
                ->setParameter('item', $item)
                ->execute();
        }

        if ($item instanceof OrderItemService) {
            $registry = $this->container->get(Registry::class);

            if (null !== $registry->repository(CarRecommendation::class)->findOneBy(['realization.id' => $item->getId()])) {
                $this->container->get(RecommendationManager::class)->recommend($item);

                return;
            }

            $em->remove($item);
        }

        foreach ($item->getChildren() as $child) {
            $this->recursiveRemove($em, $child);
        }

        $em->remove($item);
    }
}
