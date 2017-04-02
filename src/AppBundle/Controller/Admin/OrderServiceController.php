<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderService;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderServiceController extends AdminController
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

        /** @var OrderService $entity */
        $entity = parent::createNewEntity();
        $entity->setOrder($order);

        return $entity;
    }

    protected function isActionAllowed($actionName)
    {
        if (in_array($actionName, ['edit', 'delete'], true) && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderService::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    public function recommendAction()
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $orderService = $this->em->getRepository(OrderService::class)->findOneBy(['id' => $query->get('id')]);
        if (!$orderService) {
            throw new NotFoundHttpException();
        }

        $order = $orderService->getOrder();
        $order->recommendService($orderService);
        $this->em->flush();

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id'     => $order->getId(),
        ]);
    }
}
