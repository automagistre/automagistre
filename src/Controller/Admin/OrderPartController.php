<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderPart;
use App\Entity\OrderService;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPartController extends AdminController
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

        /** @var OrderPart $entity */
        $entity = parent::createNewEntity();
        $entity->setOrder($order);

        $serviceId = $this->request->query->get('order_service_id');
        if ($serviceId) {
            $service = $this->em->getRepository(OrderService::class)->findOneBy([
                'id'    => $serviceId,
                'order' => $order->getId(),
            ]);
            if (!$service) {
                throw new BadRequestHttpException('Bad order_service_id');
            }

            $entity->setOrderService($service);
        }

        return $entity;
    }

    protected function isActionAllowed($actionName)
    {
        if (in_array($actionName, ['edit', 'delete'], true) && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderPart::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
