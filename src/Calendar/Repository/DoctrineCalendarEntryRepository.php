<?php

declare(strict_types=1);

namespace App\Calendar\Repository;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Doctrine\Registry;

final class DoctrineCalendarEntryRepository implements CalendarEntryRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get(CalendarEntryId $id): ?CalendarEntry
    {
        return $this->registry->repository(CalendarEntry::class)->find($id);
    }

    public function add(CalendarEntry $entry): void
    {
        $this->registry->manager(CalendarEntry::class)->persist($entry);
    }
}
