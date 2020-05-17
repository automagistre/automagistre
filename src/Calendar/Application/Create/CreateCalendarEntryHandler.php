<?php

namespace App\Calendar\Application\Create;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryCustomerInformation;
use App\Calendar\Repository\CalendarEntryRepository;
use App\State;

final class CreateCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    private State $state;

    public function __construct(CalendarEntryRepository $repository, State $state)
    {
        $this->repository = $repository;
        $this->state = $state;
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
                $this->state->user()->uuid,
                $command->worker,
            )
        );
    }
}
