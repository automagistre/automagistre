<?php

declare(strict_types=1);

namespace App\Calendar\Application\ChangeOrder;

use App\Calendar\Repository\CalendarEntryRepository;

final class ChangeOrderCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ChangeOrderCalendarEntryCommand $command): void
    {
        $entry = $this->repository->get($command->id);

        $entry->changeOrderInfo(
            $command->orderInfo,
        );
    }
}
