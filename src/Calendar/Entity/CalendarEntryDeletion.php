<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class CalendarEntryDeletion extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(CalendarEntry $entry, DeletionReason $reason, ?string $description)
    {
        $this->id = Uuid::uuid6();
        $this->entry = $entry;
        $this->reason = $reason;
        $this->description = $description;
    }
}
