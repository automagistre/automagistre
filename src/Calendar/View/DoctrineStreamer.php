<?php

namespace App\Calendar\View;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryDeletion;
use App\Doctrine\Registry;
use App\Employee\Entity\Employee;
use function array_map;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;

final class DoctrineStreamer implements Streamer
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function byDate(DateTimeImmutable $date): StreamCollection
    {
        $entities = $this->registry->manager(CalendarEntry::class)->createQueryBuilder()
            ->select('entity.id')
            ->addSelect('entity.schedule.date')
            ->addSelect('entity.schedule.duration')
            ->addSelect('entity.customer.firstName AS firstName')
            ->addSelect('entity.customer.lastName AS lastName')
            ->addSelect('entity.customer.phone AS phone')
            ->addSelect('entity.customer.carId AS carId')
            ->addSelect('entity.customer.description AS description')
            ->addSelect('IDENTITY(entity.worker) AS workerId')
            ->from(CalendarEntry::class, 'entity')
            ->leftJoin(CalendarEntry::class, 'previous', Join::WITH, 'entity.id = previous.previous')
            ->leftJoin(CalendarEntryDeletion::class, 'deletion', Join::WITH, 'entity.id = deletion.entry')
            ->where('entity.schedule.date >= :start')
            ->andWhere('entity.schedule.date <= :end')
            ->andWhere('previous IS NULL')
            ->andWhere('deletion IS NULL')
            ->orderBy('entity.worker', 'DESC')
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult();

        $employeeRepository = $this->registry->repository(Employee::class);

        return new StreamCollection(
            array_map(
                fn (array $row) => new CalendarEntryView(
                    $row['id'],
                    $row['schedule.date'],
                    $row['schedule.duration'],
                    $row['firstName'],
                    $row['lastName'],
                    $row['phone'],
                    $row['carId'],
                    $row['description'],
                    null !== $row['workerId'] ? $employeeRepository->find($row['workerId']) : null,
                ),
                $entities
            )
        );
    }
}
