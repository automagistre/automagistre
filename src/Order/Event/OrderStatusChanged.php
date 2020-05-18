<?php

declare(strict_types=1);

namespace App\Order\Event;

use App\Order\Entity\Order;
use App\Order\Enum\OrderStatus;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method Order getSubject()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatusChanged extends GenericEvent
{
    private OrderStatus $status;

    public function __construct(Order $subject, OrderStatus $status)
    {
        parent::__construct($subject, []);

        $this->status = $status;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }
}
