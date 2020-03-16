<?php

declare(strict_types=1);

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Domain\CalendarEntryId;
use App\Calendar\Domain\DeletionReason;
use Symfony\Component\Validator\Constraints as Assert;

final class CalendarEntryDeletionDto
{
    public CalendarEntryId $id;

    /**
     * @Assert\NotBlank()
     */
    public ?DeletionReason $reason = null;

    public ?string $description = null;

    public function __construct(CalendarEntryId $id, ?DeletionReason $reason = null, ?string $description = null)
    {
        $this->id = $id;
        $this->reason = $reason;
        $this->description = $description;
    }
}
