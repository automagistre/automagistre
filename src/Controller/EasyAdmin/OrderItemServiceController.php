<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\CarModel;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemService;
use App\Form\Model\OrderService;
use App\Manager\RecommendationManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemServiceController extends OrderItemController
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
        if (null === $orderService) {
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

    protected function createNewEntity(): OrderService
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderService();
        $model->order = $order;
        $model->worker = $order->getActiveWorker();

        $parent = $this->getEntity(OrderItem::class);
        if ($parent instanceof OrderItem) {
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
        $entity->setParent($model->parent);
        $entity->setWorker($model->worker);
        $entity->setWarranty($model->warranty);

        parent::persistEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (\in_array($actionName, ['edit', 'delete'], true) && null !== $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(OrderItemService::class)->find($id);

            return $entity->getOrder()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;

        $qb = $this->em->getRepository(OrderItemService::class)
            ->createQueryBuilder('entity')
            ->orderBy('COUNT(entity.service)', 'DESC')
            ->addOrderBy('entity.service', 'ASC')
            ->setMaxResults(15);

        $car = $this->getEntity(Car::class);
        $order = $this->getEntity(Order::class);
        if (null === $car && $order instanceof Order) {
            $car = $order->getCar();
        }

        if ($car instanceof Car && ($carModel = $car->getCarModel()) instanceof CarModel) {
            $qb->leftJoin('entity.order', 'order')
                ->leftJoin('order.car', 'car')
                ->andWhere('car.carModel = :carModel')
                ->setParameter('carModel', $carModel);
        }

        if ($request->query->getBoolean('textOnly')) {
            $qb->groupBy('entity.service');
        }

        foreach (\explode(' ', \trim($request->query->get('query'))) as $key => $searchString) {
            $searchString = \trim($searchString);
            if ('' === $searchString) {
                continue;
            }

            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('entity.service', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        $data = \array_map(function (OrderItemService $entity) {
            $price = $entity->getPrice();

            return [
                'id' => $entity->getId(),
                'text' => \sprintf('%s (%s)', $entity->getService(), $this->formatMoney($price)),
                'price' => $this->formatMoney($price, true),
            ];
        }, $qb->getQuery()->getResult());

        return $this->json(['results' => $data]);
    }
}
