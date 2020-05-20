<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CalendarEntryDeletion
{
    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity=CalendarEntry::class, inversedBy="deletion")
     */
    private CalendarEntry $entry;

    /**
     * @ORM\Column(type="deletion_reason")
     */
    private DeletionReason $reason;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    public function __construct(CalendarEntry $entry, DeletionReason $reason, ?string $description)
    {
        $this->entry = $entry;
        $this->reason = $reason;
        $this->description = $description;
    }
}
