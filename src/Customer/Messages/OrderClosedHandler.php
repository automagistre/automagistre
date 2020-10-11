<?php

declare(strict_types=1);

namespace App\Customer\Messages;

use App\Customer\Entity\CustomerStorage;
use App\Customer\Enum\CustomerTransactionSource;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderStorage;
use App\Order\Messages\OrderClosed;

final class OrderClosedHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    private CustomerStorage $customerStorage;

    public function __construct(OrderStorage $orderStorage, CustomerStorage $customerStorage)
    {
        $this->orderStorage = $orderStorage;
        $this->customerStorage = $customerStorage;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        $customerId = $order->getCustomerId();
        if (null === $customerId) {
            return;
        }

        $customer = $this->customerStorage->getTransactional($customerId);

        foreach ($order->getPayments() as $payment) {
            $customer->addTransaction(
                $payment->getMoney(),
                CustomerTransactionSource::orderPrepay(),
                $order->toId()->toUuid()
            );
        }

        $customer->addTransaction(
            $order->getTotalPrice(true)->negative(),
            CustomerTransactionSource::orderPayment(),
            $order->toId()->toUuid(),
        );
    }
}
