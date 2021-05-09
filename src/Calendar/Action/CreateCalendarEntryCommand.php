<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;

/**
 * @psalm-immutable
 */
final class CreateCalendarEntryCommand
{
    public Schedule $schedule;

    public OrderInfo $orderInfo;

    public function __construct(public CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo)
    {
        $this->schedule = clone $schedule;
        $this->orderInfo = clone $orderInfo;
    }
}
