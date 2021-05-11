<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Repository\CalendarEntryRepository;
use App\MessageBus\MessageHandler;

final class ChangeOrderCalendarEntryHandler implements MessageHandler
{
    public function __construct(private CalendarEntryRepository $repository)
    {
    }

    public function __invoke(ChangeOrderCalendarEntryCommand $command): void
    {
        $entry = $this->repository->get($command->id);

        $entry->changeOrderInfo(
            $command->orderInfo,
        );
    }
}
