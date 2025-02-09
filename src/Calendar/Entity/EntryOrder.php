<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Order\Entity\OrderId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar_entry_order")
 */
class EntryOrder extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private CalendarEntryId $entryId;

    /**
     * @ORM\Column
     */
    private OrderId $orderId;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(CalendarEntryId $entryId, OrderId $orderId)
    {
        $this->id = Uuid::uuid6();
        $this->entryId = $entryId;
        $this->orderId = $orderId;
    }
}
