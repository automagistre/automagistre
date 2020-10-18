<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Customer\Entity\CustomerStorage;
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

        $customerId = $order->getCustomerId();
        $balance = null === $customerId
            ? null
            : $this->customerStorage->view($customerId)->balance;

        $order->close($balance);
    }
}
