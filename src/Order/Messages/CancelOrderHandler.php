<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderStorage;

final class CancelOrderHandler implements MessageHandler
{
    private OrderStorage $orderStorage;

    public function __construct(OrderStorage $orderStorage)
    {
        $this->orderStorage = $orderStorage;
    }

    public function __invoke(CancelOrder $command): void
    {
        $order = $this->orderStorage->get($command->orderId);

        $order->cancel();
    }
}
