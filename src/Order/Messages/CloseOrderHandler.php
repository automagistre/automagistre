<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Customer\Entity\CustomerStorage;
use App\Customer\Enum\CustomerTransactionSource;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderStorage;
use function Sentry\captureMessage;
use function sprintf;

final class CloseOrderHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    private CustomerStorage $customerStorage;

    public function __construct(OrderStorage $orderStorage, CustomerStorage $customerStorage)
    {
        $this->orderStorage = $orderStorage;
        $this->customerStorage = $customerStorage;
    }

    public function __invoke(CloseOrderCommand $command): void
    {
        $order = $this->orderStorage->get($command->orderId);

        if (!$order->isReadyToClose()) {
            captureMessage(sprintf('Requested to close order that not yet ready to close. ID: %s', $command->orderId->toString()));

            return;
        }

        $balance = null;

        $customerId = $order->getCustomerId();

        if (null !== $customerId) {
            $balance = $this->customerStorage->view($customerId)->balance;
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

        $order->close(
            $balance,
            $command->satisfaction,
        );
    }
}
