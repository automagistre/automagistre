<?php

namespace App\Calendar\Infrastructure;

use App\Calendar\Application\CalendarEntryView;
use App\Calendar\Application\StreamCollection;
use App\Calendar\Application\Streamer;
use App\Calendar\Domain\CalendarEntry;
use App\Calendar\Domain\CalendarEntryDeletion;
use App\Doctrine\Registry;
use App\Entity\Tenant\Employee;
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
            ->addSelect('entity.date')
            ->addSelect('entity.duration')
            ->addSelect('entity.customer.firstName AS firstName')
            ->addSelect('entity.customer.lastName AS lastName')
            ->addSelect('entity.customer.phone AS phone')
            ->addSelect('entity.customer.carId AS carId')
            ->addSelect('entity.customer.description AS description')
            ->addSelect('IDENTITY(entity.worker) AS workerId')
            ->from(CalendarEntry::class, 'entity')
            ->leftJoin(CalendarEntry::class, 'previous', Join::WITH, 'entity.id = previous.previous')
            ->leftJoin(CalendarEntryDeletion::class, 'deletion', Join::WITH, 'entity.id = deletion.entry')
            ->where('entity.date >= :start')
            ->andWhere('entity.date <= :end')
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
                    $row['date'],
                    $row['duration'],
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
