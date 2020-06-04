<?php

declare(strict_types=1);

namespace App\Calendar\Exception;

use App\Calendar\Entity\CalendarEntryId;
use DomainException;
use function sprintf;

final class EntryDeleted extends DomainException
{
    public static function fromEntryId(CalendarEntryId $entryId): self
    {
        return new self(
            sprintf('Cannot modify deleted entry "%s"', $entryId->toString())
        );
    }
}
