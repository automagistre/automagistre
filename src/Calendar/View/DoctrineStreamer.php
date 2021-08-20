<?php

declare(strict_types=1);

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;
use App\Doctrine\Registry;
use DateTimeImmutable;

final class DoctrineStreamer implements Streamer
{
    public function __construct(private Registry $registry)
    {
    }

    public function byDate(DateTimeImmutable $date): StreamCollection
    {
        $entities = $this->registry->manager(EntryView::class)
            ->createQueryBuilder()
            ->select('entry')
            ->from(EntryView::class, 'entry')
            ->where('entry.schedule.date >= :start')
            ->andWhere('entry.schedule.date <= :end')
            ->orderBy('entry.orderInfo.workerId')
            ->addOrderBy('entry.id')
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult()
        ;

        return new StreamCollection($entities);
    }
}
