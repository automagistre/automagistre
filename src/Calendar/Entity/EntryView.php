<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Order\Entity\OrderId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="calendar_entry_view")
 *
 * @psalm-suppress MissingConstructor
 */
class EntryView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="calendar_entry_id")
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
     * @ORM\Column(type="order_id")
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
