<?php

namespace App\Calendar\Application\Delete;

use App\Calendar\Repository\CalendarEntryRepository;
use App\State;
use DomainException;
use function sprintf;

final class DeleteCalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    private State $state;

    public function __construct(CalendarEntryRepository $repository, State $state)
    {
        $this->repository = $repository;
        $this->state = $state;
    }

    public function __invoke(DeleteCalendarEntryCommand $command): void
    {
        $entity = $this->repository->get($command->id);
        if (null === $entity) {
            throw new DomainException(sprintf('Can\'t delete "%s" CalendarEntry, as it doesn\'t exists', $command->id->toString()));
        }

        $entity->delete($command->reason, $command->description, $this->state->userId());
    }
}
