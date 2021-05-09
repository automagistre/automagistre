<?php

declare(strict_types=1);

namespace App\Part\Event;

use App\MessageBus\Async;
use App\Order\Entity\ReservationId;

/**
 * @psalm-immutable
 */
final class PartReserved implements Async
{
    public function __construct(public ReservationId $reservationId)
    {
    }
}
