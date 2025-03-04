<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Car\Manager\RecommendationManager;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemService;
use App\Order\Form\OrderService;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function array_map;
use function assert;
use function explode;
use function mb_strtolower;
use function sprintf;
use function trim;

/**
 * @psalm-suppress PropertyNotSetInConstructor
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

        $recommendationManager = $this->container->get(RecommendationManager::class);

        if (
            null === $recommendationManager->findOldRecommendation($orderItemService)
            && null === $orderItemService->workerId
        ) {
            $this->addFlash(
                'error',
                sprintf(
                    'Перед перенесом работы "%s" в рекомендации нужно выбрать исполнителя.',
                    $orderItemService->service,
                ),
            );

            return $this->redirectToReferrer();
        }

        $recommendationManager->recommend($orderItemService);

        return $this->redirectToReferrer();
    }

    protected function createNewEntity(): OrderService
    {
        $order = $this->findEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order not found');
        }

        $model = new OrderService();
        $model->order = $order;
        $model->workerId = $order->getWorkerPersonId();

        $parent = $this->findEntity(OrderItem::class);

        if ($parent instanceof OrderItem) {
            $model->parent = $parent;
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): OrderItemService
    {
        $dto = $entity;
        assert($dto instanceof OrderService);

        $entity = new OrderItemService(
            Uuid::uuid6(),
            $dto->order,
            $dto->service,
            $dto->price,
            $dto->discount,
            $dto->workerId,
            $dto->warranty,
            $dto->parent,
        );

        parent::persistEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $car = $this->findEntity(Car::class);

        if (!$car instanceof Car) {
            throw new LogicException('Car required.');
        }

        $qb->join('entity.order', 'orders')
            ->andWhere('orders.carId = :car')
            ->setParameter('car', $car->toId())
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['car'] = $this->findEntity(Car::class);
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
        $dqlFilter = null,
    ): QueryBuilder {
        $car = $this->findEntity(Car::class);

        if (!$car instanceof Car) {
            throw new LogicException('Car required.');
        }

        $qb = $this->createListQueryBuilder($entityClass, $sortDirection)
        ;

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(entity.service)', $key),
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
        }

        $qb
            ->orderBy('entity.createdAt', 'ASC')
            ->addOrderBy('orders.id', 'DESC')
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;
        $connection = $this->em->getConnection();

        $qb = $connection->createQueryBuilder()
            ->select('MIN(service) service, price_amount price, price_currency_code currency')
            ->from('order_item_service')
            ->groupBy('service', 'price_amount', 'price_currency_code')
            ->orderBy('COUNT(service)', 'DESC')
            ->setMaxResults(15)
        ;

        foreach (explode(' ', trim((string) $request->query->get('query'))) as $key => $searchString) {
            $searchString = trim($searchString);

            if ('' === $searchString) {
                continue;
            }

            $key = 'search_'.$key;

            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('LOWER(service)', ':'.$key),
            ));

            $qb->setParameter($key, '%'.mb_strtolower($searchString).'%');
        }

        $result = $connection->executeQuery($qb->getSQL(), $qb->getParameters(), $qb->getParameterTypes());

        $data = array_map(function (array $row): array {
            $price = new Money($row['price'], new Currency($row['currency']));

            return [
                'text' => sprintf('%s (%s)', $row['service'], $this->formatMoney($price)),
                'price' => $this->formatMoney($price, true),
            ];
        }, $result->fetchAllAssociative());

        return $this->json(['results' => $data]);
    }
}
