<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Form\Model\OrderPart;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Part\Entity\Part;
use App\PartPrice\PartPrice;
use App\Storage\Exception\ReservationException;
use App\Storage\Manager\ReservationManager;
use function assert;
use Closure;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartController extends OrderItemController
{
    private ReservationManager $reservationManager;

    private PartPrice $partPrice;

    public function __construct(ReservationManager $reservationManager, PartPrice $partPrice)
    {
        $this->reservationManager = $reservationManager;
        $this->partPrice = $partPrice;
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

        $model = $this->createWithoutConstructor(OrderPart::class);
        $model->order = $order;

        $part = $this->getEntity(Part::class);
        if ($part instanceof Part) {
            $model->partId = $part->toId();
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
    protected function persistEntity($orderItemPart): OrderItemPart
    {
        $model = $orderItemPart;
        assert($model instanceof OrderPart);

        $orderItemPart = new OrderItemPart($model->order, $model->partId, $model->quantity);
        $orderItemPart->setParent($model->parent);
        $orderItemPart->setWarranty($model->warranty);
        $orderItemPart->setSupplier($model->supplier);
        $orderItemPart->setPrice($model->price, $this->partPrice);

        parent::persistEntity($orderItemPart);

        try {
            $this->reservationManager->reserve($orderItemPart);
        } catch (ReservationException $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $orderItemPart;
    }

    protected function createEditDto(Closure $callable): ?object
    {
        $entity = $this->registry->getBy(OrderItemPart::class, ['id' => $this->request->query->get('id')]);

        $price = $entity->getPrice();
        $discount = $entity->discount();
        if ($discount->isPositive()) {
            $price = $price->subtract($discount);
        }

        return new OrderPart(
            $entity->getOrder(),
            $entity->getParent(),
            $entity->getPartId(),
            $entity->getQuantity(),
            $price,
            $entity->isWarranty(),
            $entity->getSupplier(),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof OrderPart);
        $entity = $this->registry->getBy(OrderItemPart::class, ['id' => $this->request->query->get('id')]);

        $entity->setParent($dto->parent);
        $entity->setPrice($dto->price, $this->partPrice);
        $entity->setQuantity($dto->quantity);
        $entity->setWarranty($dto->warranty);
        $entity->setSupplier($dto->supplier);

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
