<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemGroup;
use App\Form\Model\OrderGroup;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemGroupController extends AdminController
{
    protected function createNewEntity()
    {
        $orderId = $this->request->query->get('order_id');
        if (!$orderId) {
            throw new BadRequestHttpException('Order_id is required');
        }

        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $model = new OrderGroup();
        $model->order = $order;

        if ($parentId = $this->request->query->get('parent_id')) {
            $model->parent = $this->em->getRepository(OrderItem::class)->find($parentId);
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
