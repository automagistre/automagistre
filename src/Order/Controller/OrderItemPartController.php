<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Form\Model\OrderPart;
use App\Manager\ReservationException;
use App\Manager\ReservationManager;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Part\Domain\Part;
use function assert;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartController extends OrderItemController
{
    private ReservationManager $reservationManager;

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
            $this->reservationManager->reserve($item);
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
            $this->reservationManager->deReserve($item, $item->getQuantity());
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

        $part = $this->getEntity(Part::class);
        if ($part instanceof Part) {
            $model->part = $part;
        }

        $parent = $this->getEntity(OrderItem::class);
        if ($parent instanceof OrderItem) {
            $model->parent = $parent;
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): OrderItemPart
    {
        $model = $entity;
        assert($model instanceof OrderPart);

        $entity = new OrderItemPart($model->order, $model->part, $model->quantity, $model->price);
        $entity->setParent($model->parent);
        $entity->setWarranty($model->warranty);
        $entity->discount($model->discount);
        $entity->setSupplier($model->supplier);

        if (!$entity->isDiscounted() && $model->part->isDiscounted()) {
            $entity->discount($model->part->discount());
        }

        parent::persistEntity($entity);

        try {
            $this->reservationManager->reserve($entity);
        } catch (ReservationException $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        assert($entity instanceof OrderItemPart);

        parent::updateEntity($entity);

        try {
            $this->reservationManager->reserve($entity);
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }
    }

    protected function createEditForm($entity, array $entityProperties)
    {
        $fb = $this->createEntityFormBuilder($entity, 'edit');

        return $fb->getForm();
    }
}
