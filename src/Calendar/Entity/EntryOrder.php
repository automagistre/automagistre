<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Order\Entity\OrderId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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

    public function __construct(CalendarEntryId $entryId, OrderId $orderId)
    {
        $this->id = Uuid::uuid6();
        $this->entryId = $entryId;
        $this->orderId = $orderId;
    }
}
