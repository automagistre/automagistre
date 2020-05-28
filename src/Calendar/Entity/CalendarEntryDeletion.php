<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class CalendarEntryDeletion
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntry::class, inversedBy="deletion")
     * @ORM\JoinColumn(nullable=false)
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
        $this->id = Uuid::uuid6();
        $this->entry = $entry;
        $this->reason = $reason;
        $this->description = $description;
    }
}
