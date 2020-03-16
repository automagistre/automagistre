<?php

declare(strict_types=1);

namespace App\Calendar\Domain\Command;

use App\Calendar\Domain\CalendarEntryId;
use App\Calendar\Domain\DeletionReason;

/**
 * @psalm-immutable
 */
final class DeleteCalendarEntryCommand
{
    public CalendarEntryId $id;

    public DeletionReason $reason;

    public ?string $description;

    public function __construct(CalendarEntryId $id, DeletionReason $reason, ?string $description)
    {
        $this->id = $id;
        $this->reason = $reason;
        $this->description = $description;
    }
}
