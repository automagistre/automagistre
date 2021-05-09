<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;

/**
 * @psalm-immutable
 */
final class ChangeOrderCalendarEntryCommand
{
    public OrderInfo $orderInfo;

    public function __construct(public CalendarEntryId $id, OrderInfo $orderInfo)
    {
        $this->orderInfo = clone $orderInfo;
    }
}
