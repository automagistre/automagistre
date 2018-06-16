<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemPart;
use App\Form\Model\OrderPart;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemPartControllerEasy extends AbstractController
{
    protected function createNewEntity(): OrderPart
    {
        if (!$order = $this->getEntity(Order::class)) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderPart();
        $model->order = $order;

        if ($parent = $this->getEntity(OrderItem::class)) {
            $model->parent = $parent;
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
