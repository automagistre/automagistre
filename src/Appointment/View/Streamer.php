<?php

namespace App\Appointment\View;

use App\Appointment\Entity\Appointment;
use App\Doctrine\Registry;
use DateTimeImmutable;

final class Streamer
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
        $entities = $this->registry->manager(Appointment::class)->createQueryBuilder()
            ->select('entity')
            ->from(Appointment::class, 'entity')
            ->join('entity.order', 'order')
            ->where('entity.date >= :start')
            ->andWhere('entity.date <= :end')
            ->orderBy('order.worker', 'DESC')
            ->setParameter('start', $date->setTime(0, 0, 0), 'datetime')
            ->setParameter('end', $date->setTime(23, 59, 59), 'datetime')
            ->getQuery()
            ->getResult();

        return new StreamCollection($entities);
    }
}
