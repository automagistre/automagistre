<?php

declare(strict_types=1);

namespace App\Calendar\Event;

use App\Calendar\Entity\CalendarEntryId;

/**
 * @psalm-immutable
 */
final class EntryScheduled
{
    public CalendarEntryId $id;

    public function __construct(CalendarEntryId $id)
    {
        $this->id = $id;
    }
}
