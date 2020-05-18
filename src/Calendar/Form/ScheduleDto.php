<?php

namespace App\Calendar\Form;

use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class ScheduleDto
{
    /**
     * @var DateTimeImmutable
     *
     * @Assert\NotBlank
     */
    public $date;

    /**
     * @var DateInterval
     *
     * @Assert\NotBlank
     */
    public $duration;

    public function __construct(DateTimeImmutable $date, DateInterval $duration)
    {
        $this->date = $date;
        $this->duration = $duration;
    }
}
