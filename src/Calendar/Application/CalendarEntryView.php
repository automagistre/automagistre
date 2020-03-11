<?php

namespace App\Calendar\Application;

use App\Calendar\Domain\CalendarEntryId;
use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class CalendarEntryView
{
    public CalendarEntryId $id;

    public DateTimeImmutable $date;

    public DateInterval $duration;

    public ?string $description;

    public ?Employee $worker;

    public function __construct(
        CalendarEntryId $id,
        DateTimeImmutable $date,
        DateInterval $duration,
        ?string $description = null,
        ?Employee $worker = null
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->duration = $duration;
        $this->description = $description;
        $this->worker = $worker;
    }
}
