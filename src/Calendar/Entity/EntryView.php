<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Order\Entity\OrderId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="calendar_entry_view")
 *
 * @psalm-suppress MissingConstructor
 */
class EntryView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public CalendarEntryId $id;

    /**
     * @ORM\Embedded(class=Schedule::class)
     */
    public Schedule $schedule;

    /**
     * @ORM\Embedded(class=OrderInfo::class)
     */
    public OrderInfo $orderInfo;

    /**
     * @ORM\Column
     */
    public ?OrderId $orderId;

    public function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo, OrderId $orderId = null)
    {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->orderInfo = $orderInfo;
        $this->orderId = $orderId;
    }
}
