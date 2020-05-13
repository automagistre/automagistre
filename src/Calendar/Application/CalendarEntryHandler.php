<?php

declare(strict_types=1);

namespace App\Calendar\Application;

use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryCustomerInformation;
use App\Calendar\Domain\CalendarEntryDeletion;
use App\Calendar\Domain\CalendarEntryRepository;
use App\Calendar\Domain\Command\CreateCalendarEntryCommand;
use App\Calendar\Domain\Command\DeleteCalendarEntryCommand;
use App\Calendar\Domain\Command\RescheduleCalendarEntryCommand;
use App\Doctrine\Registry;
use App\State;
use DomainException;
use function sprintf;

final class CalendarEntryHandler
{
    private CalendarEntryRepository $repository;

    private State $state;

    private Registry $registry;

    public function __construct(CalendarEntryRepository $repository, State $state, Registry $registry)
    {
        $this->repository = $repository;
        $this->state = $state;
        $this->registry = $registry;
    }

    public function create(CreateCalendarEntryCommand $command): void
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

    public function reschedule(RescheduleCalendarEntryCommand $command): void
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

    public function delete(DeleteCalendarEntryCommand $command): void
    {
        $entity = $this->repository->get($command->id);
        if (null === $entity) {
            throw new DomainException(sprintf('Can\'t delete "%s" CalendarEntry, as it doesn\'t exists', $command->id->toString()));
        }

        // TODO Разобраться почему Doctrine не хочет CreatedBy через \App\CreatedBy\EventListener\PostPersistEventListener сохранять
        $this->registry->manager(CalendarEntryDeletion::class)->persist(
            $entity->delete($command->reason, $command->description, $this->state->userId())
        );
    }
}
