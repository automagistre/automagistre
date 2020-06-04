<?php

declare(strict_types=1);

namespace App\Calendar\Application\ChangeOrder;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;

/**
 * @psalm-immutable
 */
final class ChangeOrderCalendarEntryCommand
{
    public CalendarEntryId $id;

    public OrderInfo $orderInfo;

    public function __construct(CalendarEntryId $id, OrderInfo $orderInfo)
    {
        $this->id = $id;
        $this->orderInfo = clone $orderInfo;
    }
}
