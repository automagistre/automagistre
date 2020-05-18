<?php

declare(strict_types=1);

namespace App\Calendar\Repository;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Exception\CalendarEntryNotFound;
use App\Calendar\Form\CalendarEntryDto;
use App\Shared\Doctrine\Registry;

final class DoctrineCalendarEntryRepository implements CalendarEntryRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get(CalendarEntryId $id): CalendarEntry
    {
        $entity = $this->registry->findBy(CalendarEntry::class, ['id' => $id]);

        if (null === $entity) {
            throw CalendarEntryNotFound::byId($id);
        }

        return $entity;
    }

    public function add(CalendarEntry $entry): void
    {
        $this->registry->manager(CalendarEntry::class)->persist($entry);
    }

    public function view(CalendarEntryId $id): CalendarEntryDto
    {
        return CalendarEntryDto::fromArray($this->registry->view($id));
    }
}
