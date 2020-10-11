<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Shared\Doctrine\Registry;

final class OrderStorage
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get(OrderId $orderId): Order
    {
        return $this->registry->get(Order::class, $orderId);
    }
}
