<?php

namespace App\Calendar\Application\Create;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryCustomerInformation;
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
        $this->repository->add(
            CalendarEntry::create(
                $command->date,
                $command->duration,
                new CalendarEntryCustomerInformation(
                    $command->firstName,
                    $command->lastName,
                    $command->phone,
                    $command->carId,
                    $command->description,
                ),
                $command->worker,
            )
        );
    }
}
