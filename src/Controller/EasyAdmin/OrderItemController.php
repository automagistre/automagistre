<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\CarRecommendation;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Entity\OrderItemService;
use App\Entity\Reservation;
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
     * @param OrderItem $entity
     */
    protected function removeEntity($entity): void
    {
        $removeReservation = function (EntityManagerInterface $em, $child): void {
            $em->createQueryBuilder()
                ->delete()
                ->from(Reservation::class, 'entity')
                ->where('entity.orderItemPart = :item')
                ->getQuery()
                ->setParameters([
                    'item' => $child,
                ])
                ->execute();
        };

        $this->em->transactional(function (EntityManagerInterface $em) use ($entity, $removeReservation): void {
            foreach ($entity->getChildren() as $child) {
                if ($child instanceof OrderItemPart) {
                    $removeReservation($em, $child);
                } elseif ($child instanceof OrderItemService) {
                    $em->createQueryBuilder()
                        ->delete()
                        ->from(CarRecommendation::class, 'entity')
                        ->where('entity.realization = :item')
                        ->getQuery()
                        ->setParameters([
                            'item' => $entity,
                        ])
                        ->execute();

                    foreach ($child->getChildren() as $part) {
                        if (!$part instanceof OrderItemPart) {
                            throw new LogicException('Part expected.');
                        }

                        $removeReservation($em, $part);
                    }
                }

                $em->remove($child);
            }

            parent::removeEntity($entity);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        $parameters['order'] = $this->getEntity(Order::class);

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
