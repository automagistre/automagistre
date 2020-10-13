<?php

namespace App\Calendar\Form;

use App\Calendar\Entity\Schedule;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
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

    public static function fromSchedule(Schedule $schedule): self
    {
        $dto = new self();
        $dto->date = $schedule->date;
        $dto->duration = $schedule->duration;

        return $dto;
    }
}
