<?php

declare(strict_types=1);

namespace App\Calendar\Repository;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryView;

interface CalendarEntryRepository
{
    public function get(CalendarEntryId $id): CalendarEntry;

    public function add(CalendarEntry $entry): void;

    public function view(CalendarEntryId $id): EntryView;
}
