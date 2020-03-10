<?php

namespace App\Calendar\Infrastructure;

use App\Calendar\Application\CalendarEntryView;
use App\Calendar\Application\StreamCollection;
use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Doctrine\Registry;
use App\Entity\Tenant\Employee;
use function array_map;
use DateTimeImmutable;

final class DoctrineStreamer implements Streamer
{
    /**
     * @var Registry
     */
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function byDate(DateTimeImmutable $date): StreamCollection
    {
        $entities = $this->registry->manager(CalendarEntry::class)->createQueryBuilder()
            ->select('entity.date, entity.duration, entity.description')
            ->addSelect('worker.id AS workerId')
            ->from(CalendarEntry::class, 'entity')
            ->leftJoin('entity.worker', 'worker')
            ->where('entity.date >= :start')
            ->andWhere('entity.date <= :end')
            ->orderBy('worker.id', 'DESC')
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult();

        $employeeRepository = $this->registry->repository(Employee::class);

        return new StreamCollection(
            array_map(
                fn (array $row) => new CalendarEntryView(
                    $row['date'],
                    $row['duration'],
                    $row['description'],
                    null !== $row['workerId'] ? $employeeRepository->find($row['workerId']) : null,
                ),
                $entities
            )
        );
    }
}
