<?php

namespace App\Calendar\Exception;

use App\Calendar\Entity\CalendarEntry;
use App\Calendar\Entity\CalendarEntryId;
use InvalidArgumentException;
use function sprintf;

final class CalendarEntryNotFound extends InvalidArgumentException
{
    public static function byId(CalendarEntryId $id): self
    {
        return new self(sprintf('%s with id %s not found.', CalendarEntry::class, $id->toString()));
    }
}
