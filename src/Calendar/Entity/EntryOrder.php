<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Order\Entity\OrderId;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar_entry_order")
 */
class EntryOrder
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="calendar_entry_id")
     */
    private CalendarEntryId $entryId;

    /**
     * @ORM\Column(type="order_id")
     */
    private OrderId $orderId;

    public function __construct(CalendarEntryId $entryId, OrderId $orderId)
    {
        $this->id = Uuid::uuid6();
        $this->entryId = $entryId;
        $this->orderId = $orderId;
    }
}
