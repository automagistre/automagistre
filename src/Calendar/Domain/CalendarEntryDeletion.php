<?php

declare(strict_types=1);

namespace App\Calendar\Domain;

use App\User\Domain\UserId;
use DateTimeImmutable;
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
     */
    private ?CalendarEntry $entry;

    /**
     * @ORM\Column(type="deletion_reason")
     */
    private DeletionReason $reason;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="user_id")
     */
    private UserId $deletedBy;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $deletedAt;

    public function __construct(CalendarEntry $entry, DeletionReason $reason, ?string $description, UserId $deletedBy)
    {
        $this->id = Uuid::uuid4();
        $this->entry = $entry;
        $this->reason = $reason;
        $this->description = $description;
        $this->deletedBy = $deletedBy;
        $this->deletedAt = new DateTimeImmutable();
    }
}
