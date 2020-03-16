<?php

declare(strict_types=1);

namespace App\Calendar\Infrastructure;

use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryId;
use App\Calendar\Domain\CalendarEntryRepository;
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
