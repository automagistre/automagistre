<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Repository\CalendarEntryRepository;

final class RescheduleCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RescheduleCalendarEntryCommand $command): void
    {
        $previous = $this->repository->get($command->previousId);

        $entity = $previous->reschedule(
            $command->id,
            $command->schedule,
            $command->orderInfo,
        );

        $this->repository->add($entity);
    }
}
