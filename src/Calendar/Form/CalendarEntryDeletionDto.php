<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Enum\DeletionReason;
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
