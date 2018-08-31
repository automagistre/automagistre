<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Form\Model\OrderPart;
use App\Manager\ReservationException;
use App\Manager\ReservationManager;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartController extends OrderItemController
{
    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(ReservationManager $reservationManager)
    {
        $this->reservationManager = $reservationManager;
    }

    public function reserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);
        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->reserve($item->getPart(), $item->getQuantity(), $item->getOrder());
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    public function deReserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);
        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->deReserve($item->getPart(), $item->getQuantity(), $item->getOrder());
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    protected function createNewEntity(): OrderPart
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderPart();
        $model->order = $order;

        $parent = $this->getEntity(OrderItem::class);
        if ($parent instanceof OrderItem) {
            $model->parent = $parent;
        }

        return $model;
    }

    /**
     * @param OrderPart $model
     */
    protected function persistEntity($model): void
    {
        $entity = new OrderItemPart($model->order, $model->part, $model->quantity, $model->price, $this->getUser());
        $entity->setParent($model->parent);
        $entity->setWarranty($model->warranty);

        parent::persistEntity($entity);

        try {
            $this->reservationManager->reserve($entity->getPart(), $entity->getQuantity(), $entity->getOrder());
        } catch (ReservationException $e) {
            $this->addFlash('warning', $e->getMessage());
        }
    }

    /**
     * @param OrderItemPart $entity
     */
    protected function updateEntity($entity): void
    {
        $part = $entity->getPart();
        $order = $entity->getOrder();

        $reserved = $this->reservationManager->reserved($part, $order);
        if (0 < $reserved) {
            $this->reservationManager->deReserve($part, $reserved, $order);
        }

        parent::updateEntity($entity);

        try {
            $this->reservationManager->reserve($part, $entity->getQuantity(), $order);
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (\in_array($actionName, ['edit', 'delete'], true) && null !== $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderItemPart::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
