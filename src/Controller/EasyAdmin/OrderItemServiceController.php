<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarModel;
use App\Entity\Landlord\Organization;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemService;
use App\Form\Model\OrderService;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemServiceController extends OrderItemController
{
    public function recommendAction(): RedirectResponse
    {
        if (!$this->request->isMethod('POST')) {
            throw new BadRequestHttpException();
        }

        $query = $this->request->query;

        $orderItemService = $this->em->getRepository(OrderItemService::class)->findOneBy(['id' => $query->get('id')]);
        if (!$orderItemService instanceof OrderItemService) {
            throw new NotFoundHttpException();
        }

        if (
            null === $this->recommendationManager->findOldRecommendation($orderItemService)
            && null === $orderItemService->getWorker()
        ) {
            $this->addFlash(
                'error',
                \sprintf(
                    'Перед перенесом работы "%s" в рекоммендации нужно выбрать исполнителя.',
                    $orderItemService->getService()
                )
            );

            return $this->redirectToReferrer();
        }

        $this->recommendationManager->recommend($orderItemService);

        return $this->redirectToReferrer();
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
        $entity = new OrderItemService($model->order, $model->service, $model->price, $this->getUser());
        $entity->setParent($model->parent);
        $entity->setWorker($model->worker);
        $entity->setWarranty($model->warranty);

        parent::persistEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $car = $this->getEntity(Car::class);
        if (!$car instanceof Car) {
            throw new LogicException('Car required.');
        }

        $qb->join('entity.order', 'orders')
            ->join('orders.car', 'car')
            ->andWhere('car = :car')
            ->setParameter('car', $car);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['car'] = $this->getEntity(Car::class);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $car = $this->getEntity(Car::class);
        if (!$car instanceof Car) {
            throw new LogicException('Car required.');
        }

        $qb = $this->createListQueryBuilder($entityClass, $sortDirection)
            ->leftJoin(Person::class, 'person', Join::WITH, 'person = entity.worker')
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization = entity.worker');

        foreach (\explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('entity.service', $key),
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        $qb
            ->orderBy('orders.closedAt', 'ASC')
            ->addOrderBy('orders.id', 'DESC');

        return $qb;
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
