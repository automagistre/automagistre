<?php

declare(strict_types=1);

namespace App\Calendar\Action;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Enum\DeletionReason;

/**
 * @psalm-immutable
 */
final class DeleteCalendarEntryCommand
{
    public function __construct(public CalendarEntryId $id, public DeletionReason $reason, public ?string $description)
    {
    }
}
