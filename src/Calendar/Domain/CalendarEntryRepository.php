<?php

declare(strict_types=1);

namespace App\Calendar\Domain;

interface CalendarEntryRepository
{
    public function get(CalendarEntryId $id): ?CalendarEntry;

    public function add(CalendarEntry $entry): void;
}
