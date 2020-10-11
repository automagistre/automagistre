<?php

declare(strict_types=1);

namespace App\Employee\Messages;

use App\Customer\Entity\CustomerStorage;
use App\Customer\Enum\CustomerTransactionSource;
use App\Employee\Entity\Employee;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderContractor;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderStorage;
use App\Order\Messages\OrderClosed;
use App\Shared\Doctrine\Registry;

final class OrderClosedHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    private CustomerStorage $customerStorage;

    private Registry $registry;

    public function __construct(OrderStorage $orderStorage, CustomerStorage $customerStorage, Registry $registry)
    {
        $this->orderStorage = $orderStorage;
        $this->customerStorage = $customerStorage;
        $this->registry = $registry;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        foreach ($order->getItems(OrderItemService::class) as $item) {
            /** @var OrderItemService $item */
            $price = $item->getTotalPrice(true, false);
            if (!$price->isPositive()) {
                continue;
            }

            $workerId = $item->workerId;
            if (null === $workerId) {
                continue;
            }

            $em = $this->registry->manager();
            $employee = $em
                ->createQueryBuilder()
                ->select('t')
                ->from(Employee::class, 't')
                ->where('t.personId = :personId')
                ->orderBy('t.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->setParameter('personId', $workerId)
                ->getOneOrNullResult();

            if (!$employee instanceof Employee) {
                $em->persist(new OrderContractor($order->toId(), $workerId, $price));

                continue;
            }

            $salary = $price->multiply($employee->getRatio() / 100);

            $worker = $this->customerStorage->getTransactional($workerId);
            $worker->addTransaction(
                $salary->absolute(),
                CustomerTransactionSource::orderSalary(),
                $order->toId()->toUuid(),
            );
        }
    }
}
