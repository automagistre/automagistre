<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemGroup;
use App\Form\Model\OrderGroup;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemGroupController extends AbstractController
{
    protected function createNewEntity()
    {
        if (!$order = $this->getEntity(Order::class)) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderGroup();
        $model->order = $order;

        if ($parent = $this->getEntity(OrderItem::class)) {
            $model->parent = $parent;
        }

        return $model;
    }

    /**
     * @param OrderGroup $model
     */
    protected function persistEntity($model): void
    {
        $entity = new OrderItemGroup($model->order, $model->name);
        if ($model->parent) {
            $entity->setParent($model->parent);
        }

        parent::persistEntity($entity);
    }
}
