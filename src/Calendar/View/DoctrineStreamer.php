<?php

namespace App\Calendar\View;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryDeletion;
use App\Calendar\Form\CalendarEntryDto;
use App\Shared\Doctrine\Registry;
use function array_map;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
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
            ->select('entity')
            ->from(CalendarEntry::class, 'entity')
            ->leftJoin(CalendarEntry::class, 'previous', Join::WITH, 'entity.id = previous.previous')
            ->leftJoin(CalendarEntryDeletion::class, 'deletion', Join::WITH, 'entity.id = deletion.entry')
            ->where('entity.schedule.date >= :start')
            ->andWhere('entity.schedule.date <= :end')
            ->andWhere('previous IS NULL')
            ->andWhere('deletion IS NULL')
            ->orderBy('entity.orderInfo.workerId')
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return new StreamCollection(
            array_map(fn (array $row) => CalendarEntryDto::fromArray($row), $entities)
        );
    }
}
