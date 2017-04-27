<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Form\Model\OrderPart;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartController extends AdminController
{
    protected function createNewEntity(): OrderPart
    {
        $orderId = $this->request->query->get('order_id');
        if (!$orderId) {
            throw new BadRequestHttpException('Order_id is required');
        }

        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $model = new OrderPart();
        $model->order = $order;

        if ($parentId = $this->request->query->get('parent_id')) {
            $model->parent = $this->em->getRepository(OrderItem::class)->find($parentId);
        }

        return $model;
    }

    /**
     * @param OrderPart $model
     */
    protected function persistEntity($model): void
    {
        $entity = new OrderItemPart($model->order, $this->getUser(), $model->part, $model->quantity, $model->price);

        if ($parent = $model->parent) {
            $entity->setParent($parent);
        }

        parent::persistEntity($entity);
    }

    protected function isActionAllowed($actionName): bool
    {
        if (in_array($actionName, ['edit', 'delete'], true) && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderItemPart::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
