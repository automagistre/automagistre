<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Entity\CalendarEntryCustomerInformation;
use App\Calendar\Repository\CalendarEntryRepository;
use DomainException;
use function sprintf;

final class RescheduleCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    public function __construct(CalendarEntryRepository $repository)
    {
        $this->repository = $repository;
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
            $command->worker,
        );

        $this->repository->add($entity);
    }
}
