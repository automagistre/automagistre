<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;

/**
 * @psalm-immutable
 */
final class RescheduleCalendarEntryCommand
{
    public CalendarEntryId $id;

    public CalendarEntryId $previousId;

    /**
     * @Assert\Valid()
     */
    public Schedule $schedule;

    /**
     * @Assert\Valid()
     */
    public OrderInfo $orderInfo;

    public function __construct(
        CalendarEntryId $id,
        CalendarEntryId $previousId,
        Schedule $schedule,
        OrderInfo $orderInfo
    ) {
        $this->id = $id;
        $this->previousId = $previousId;
        $this->schedule = clone $schedule;
        $this->orderInfo = clone $orderInfo;
    }
}
