<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="calendar_entry_view")
 */
class EntryView
{
    /**
     * @ORM\Id()
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

    public function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo)
    {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->orderInfo = $orderInfo;
    }
}
