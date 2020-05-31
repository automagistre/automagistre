<?php

namespace App\Calendar\Entity;

use const DATE_RFC3339_EXTENDED;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 *
 * @ORM\Embeddable
 */
final class Schedule
{
    private const FORMAT = '%RP%YY%MM%DDT%HH%IM%SS';

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $date;

    /**
     * @ORM\Column(type="dateinterval")
     */
    public DateInterval $duration;

    public function __construct(DateTimeImmutable $date, DateInterval $duration)
    {
        $this->date = $date;
        $this->duration = clone $duration;
    }

    public function equal(self $right): bool
    {
        $left = $this;

        return $left->date->format(DATE_RFC3339_EXTENDED) === $right->date->format(DATE_RFC3339_EXTENDED)
            && $left->duration->format(self::FORMAT) === $right->duration->format(self::FORMAT);
    }
}
