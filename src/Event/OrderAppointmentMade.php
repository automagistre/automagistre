<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Tenant\Order;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method Order getSubject()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderAppointmentMade extends GenericEvent
{
    public function __construct(Order $order, array $arguments = [])
    {
        parent::__construct($order, $arguments);
    }
}
