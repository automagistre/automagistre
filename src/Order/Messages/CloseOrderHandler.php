<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\TransactionalCustomer;
use App\Customer\Enum\CustomerTransactionSource;
use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderStorage;
use function Sentry\captureMessage;
use function sprintf;

final class CloseOrderHandler implements MessageHandler
{
    public function __construct(private OrderStorage $orderStorage, private Registry $registry)
    {
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
            $balance = $this->registry->get(CustomerView::class, $customerId)->balance;
            $customer = new TransactionalCustomer($customerId, $this->registry->manager());

            foreach ($order->getPayments() as $payment) {
                $customer->addTransaction(
                    $payment->getMoney(),
                    CustomerTransactionSource::orderPrepay(),
                    $order->toId()->toUuid(),
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
