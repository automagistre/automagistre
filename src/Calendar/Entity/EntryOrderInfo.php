<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar_entry_order_info")
 */
class EntryOrderInfo extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=CalendarEntry::class, inversedBy="orders")
     */
    private CalendarEntry $entry;

    /**
     * @ORM\Embedded(class=OrderInfo::class, columnPrefix=false)
     */
    private OrderInfo $orderInfo;

    public function __construct(CalendarEntry $entry, OrderInfo $orderInfo)
    {
        $this->id = Uuid::uuid6();
        $this->entry = $entry;
        $this->orderInfo = $orderInfo;
    }

    public function equal(OrderInfo $orderInfo): bool
    {
        return $this->orderInfo->equal($orderInfo);
    }
}
