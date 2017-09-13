<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemService;
use App\Form\Model\OrderService;
use App\Manager\RecommendationManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemServiceController extends AdminController
{
    /**
     * @var RecommendationManager
     */
    private $recommendationManager;

    public function __construct(RecommendationManager $recommendationManager)
    {
        $this->recommendationManager = $recommendationManager;
    }

    public function recommendAction(): RedirectResponse
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $orderService = $this->em->getRepository(OrderItemService::class)->findOneBy(['id' => $query->get('id')]);
        if (!$orderService) {
            throw new NotFoundHttpException();
        }

        $order = $orderService->getOrder();
        $this->recommendationManager->recommend($orderService);

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $order->getId(),
        ]);
    }

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

        $model = new OrderService();
        $model->order = $order;

        if ($parentId = $this->request->query->get('parent_id')) {
            $model->parent = $this->em->getRepository(OrderItem::class)->find($parentId);
        }

        return $model;
    }

    /**
     * @param OrderService $model
     */
    protected function persistEntity($model): void
    {
        $entity = new OrderItemService($model->order, $model->service, $model->price);
        if ($model->parent) {
            $entity->setParent($model->parent);
        }

        parent::persistEntity($entity);
    }

    protected function isActionAllowed($actionName): bool
    {
        if (in_array($actionName, ['edit', 'delete'], true) && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderItemService::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
