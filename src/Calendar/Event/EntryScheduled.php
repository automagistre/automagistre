<?php

declare(strict_types=1);

namespace App\Calendar\Event;

use App\Calendar\Entity\CalendarEntryId;

/**
 * @psalm-immutable
 */
final class EntryScheduled
{
    public function __construct(public CalendarEntryId $id)
    {
    }
}
