<?php

declare(strict_types=1);

namespace App\Calendar\Domain\Command;

use App\Calendar\Domain\CalendarEntryId;
use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class RescheduleCalendarEntryCommand
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
        ?string $description,
        ?Employee $worker
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->duration = $duration;
        $this->worker = $worker;
        $this->description = $description;
    }
}
