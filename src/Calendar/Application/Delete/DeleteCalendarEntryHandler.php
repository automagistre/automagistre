<?php

namespace App\Calendar\Application\Delete;

use App\Calendar\Repository\CalendarEntryRepository;

final class DeleteCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteCalendarEntryCommand $command): void
    {
        $entity = $this->repository->get($command->id);

        $entity->delete($command->reason, $command->description);
    }
}
