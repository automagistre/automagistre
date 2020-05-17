<?php

declare(strict_types=1);

namespace App\Calendar\Application\Delete;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Enum\DeletionReason;

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
