<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Order\Messages\OrderCancelled;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 */
class OrderCancel extends OrderClose
{
    public function __construct(Order $order)
    {
        parent::__construct($order);

        $this->record(new OrderCancelled($order->toId()));
    }
}
