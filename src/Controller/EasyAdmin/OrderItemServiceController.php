<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

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
final class OrderItemServiceController extends AbstractController
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
        if (!$order = $this->getEntity(Order::class)) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderService();
        $model->order = $order;

        if ($parent = $this->getEntity(OrderItem::class)) {
            $model->parent = $parent;
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
