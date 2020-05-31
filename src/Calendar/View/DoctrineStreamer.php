<?php

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;
use App\Shared\Doctrine\Registry;
use DateTimeImmutable;

final class DoctrineStreamer implements Streamer
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
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
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult();

        return new StreamCollection($entities);
    }
}
