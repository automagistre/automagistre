<?php

declare(strict_types=1);

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Domain\DeletionReason;
use Symfony\Component\Validator\Constraints as Assert;

final class CalendarEntryDeletionDto
{
    /**
     * @Assert\NotBlank()
     */
    public ?DeletionReason $reason = null;

    public ?string $description = null;
}
