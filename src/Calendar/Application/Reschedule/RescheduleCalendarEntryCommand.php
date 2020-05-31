<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\Schedule;

/**
 * @psalm-immutable
 */
final class RescheduleCalendarEntryCommand
{
    public CalendarEntryId $id;

    /**
     * @Assert\Valid()
     */
    public Schedule $schedule;

    public function __construct(CalendarEntryId $id, Schedule $schedule)
    {
        $this->id = $id;
        $this->schedule = clone $schedule;
    }
}
