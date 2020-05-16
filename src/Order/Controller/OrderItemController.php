<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Car\Manager\RecommendationManager;
use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Tenant\Reservation;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use function array_merge;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use function in_array;
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
        return array_merge(parent::getSubscribedServices(), [
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

        if (in_array($actionName, ['edit', 'delete'], true) && null !== $id = $this->request->get('id')) {
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
        assert($entity instanceof OrderItem);

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
            if (null !== $this->registry->repository(Recommendation::class)->findOneBy(['realization.id' => $item->getId()])) {
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
