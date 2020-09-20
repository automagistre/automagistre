<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Repository\CalendarEntryRepository;
use App\MessageBus\MessageHandler;

final class RescheduleCalendarEntryHandler implements MessageHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RescheduleCalendarEntryCommand $command): void
    {
        $entry = $this->repository->get($command->id);

        $entry->reschedule(
            $command->schedule,
        );
    }
}
