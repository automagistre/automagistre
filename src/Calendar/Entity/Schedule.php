<?php

namespace App\Calendar\Entity;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Schedule
{
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $date;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private DateInterval $duration;

    public function __construct(DateTimeImmutable $date, DateInterval $duration)
    {
        $this->date = $date;
        $this->duration = $duration;
    }
}
