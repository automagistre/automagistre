<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Repository\CalendarEntryRepository;
use App\MessageBus\MessageHandler;

final class CreateCalendarEntryHandler implements MessageHandler
{
    public function __construct(private CalendarEntryRepository $repository)
    {
    }

    public function __invoke(CreateCalendarEntryCommand $command): void
    {
        $entity = CalendarEntry::create(
            $command->id,
            $command->schedule,
            $command->orderInfo,
        );

        $this->repository->add($entity);
    }
}
