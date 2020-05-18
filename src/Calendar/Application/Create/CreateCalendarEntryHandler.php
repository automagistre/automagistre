<?php

namespace App\Calendar\Application\Create;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Repository\CalendarEntryRepository;

final class CreateCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateCalendarEntryCommand $command): void
    {
        $entity = CalendarEntry::create(
            $command->id,
            $command->schedule,
            $command->orderInfo
        );

        $this->repository->add($entity);
    }
}
