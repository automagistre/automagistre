<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Repository\CalendarEntryRepository;
use App\MessageBus\MessageHandler;

final class DeleteCalendarEntryHandler implements MessageHandler
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
