<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\Schedule;

/**
 * @psalm-immutable
 */
final class RescheduleCalendarEntryCommand
{
    public Schedule $schedule;

    public function __construct(public CalendarEntryId $id, Schedule $schedule)
    {
        $this->schedule = clone $schedule;
    }
}
