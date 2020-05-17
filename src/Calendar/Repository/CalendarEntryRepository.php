<?php

declare(strict_types=1);

namespace App\Calendar\Repository;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;

interface CalendarEntryRepository
{
    public function get(CalendarEntryId $id): ?CalendarEntry;

    public function add(CalendarEntry $entry): void;
}
