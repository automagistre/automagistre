<?php

namespace App\Calendar\Application;

use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class CalendarEntryView
{
    public DateTimeImmutable $date;

    public DateInterval $duration;

    public ?string $description;

    public ?Employee $worker;

    public function __construct(
        DateTimeImmutable $date,
        DateInterval $duration,
        ?string $description,
        ?Employee $worker
    ) {
        $this->date = $date;
        $this->duration = $duration;
        $this->description = $description;
        $this->worker = $worker;
    }
}
