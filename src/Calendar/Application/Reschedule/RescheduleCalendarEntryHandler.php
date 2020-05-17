<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Entity\CalendarEntryCustomerInformation;
use App\Calendar\Repository\CalendarEntryRepository;
use App\State;
use DomainException;
use function sprintf;

final class RescheduleCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    private State $state;

    public function __construct(CalendarEntryRepository $repository, State $state)
    {
        $this->repository = $repository;
        $this->state = $state;
    }

    public function __invoke(RescheduleCalendarEntryCommand $command): void
    {
        $previous = $this->repository->get($command->id);
        if (null === $previous) {
            throw new DomainException(sprintf('Can\'t reschedule "%s" CalendarEntry, as it doesn\'t exists', $command->id->toString()));
        }

        $entity = $previous->reschedule(
            $command->date,
            $command->duration,
            new CalendarEntryCustomerInformation(
                $command->firstName,
                $command->lastName,
                $command->phone,
                $command->carId,
                $command->description,
            ),
            $this->state->userId(),
            $command->worker,
        );

        $this->repository->add($entity);
    }
}
