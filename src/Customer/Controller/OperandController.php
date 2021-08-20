<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Balance\Entity\BalanceView;
use App\Car\Repository\CarCustomerRepository;
use App\Customer\Entity\CustomerTransactionView;
use App\EasyAdmin\Controller\AbstractController;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Payment\Manager\PaymentManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use function array_merge;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class OperandController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            PaymentManager::class,
            CarCustomerRepository::class,
        ]);
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
        $isBalanceSort = 'balance' === $sortField;

        $qb = parent::createListQueryBuilder(
            $entityClass,
            $sortDirection,
            $isBalanceSort ? null : $sortField,
            $dqlFilter,
        );

        if ($isBalanceSort) {
            $this->sortByBalance($qb, $sortDirection);
        }

        return $qb;
    }

    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $isBalanceSort = 'balance' === $sortField;

        $qb = parent::createSearchQueryBuilder(
            $entityClass,
            $searchQuery,
            $searchableFields,
            $isBalanceSort ? null : $sortField,
            $sortDirection,
            $dqlFilter,
        );

        if ($isBalanceSort) {
            $this->sortByBalance($qb, $sortDirection);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $operand = $parameters['entity'];
            /** @var CarCustomerRepository $carRepository */
            $carRepository = $this->container->get(CarCustomerRepository::class);

            $parameters['cars'] = $carRepository->carsByCustomer($operand->toId());
            $parameters['orders'] = $this->registry->repository(Order::class)
                ->findBy(['customerId' => $operand->toId()], ['id' => 'DESC'], 20)
            ;
            $parameters['payments'] = $this->registry->repository(CustomerTransactionView::class)
                ->findBy(['operandId' => $operand->toId()], ['id' => 'DESC'], 20)
            ;
            $parameters['notes'] = $this->registry->repository(NoteView::class)
                ->findBy(['subject' => $operand->toId()->toUuid()], ['id' => 'DESC'])
            ;
            $parameters['balance'] = $this->get(PaymentManager::class)->balance($operand);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    private function sortByBalance(QueryBuilder $qb, ?string $sortDirection): void
    {
        $qb
            ->leftJoin(
                BalanceView::class,
                'balance',
                Join::WITH,
                'balance.id = entity.id',
            )
            ->orderBy('balance.money.amount', $sortDirection)
            ->groupBy('entity')
            ->addGroupBy('balance.money.amount')
        ;
    }
}
