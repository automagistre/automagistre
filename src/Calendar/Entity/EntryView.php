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

    public static function sql(): string
    {
        return '
            CREATE VIEW calendar_entry_view AS
            SELECT e.id,
                   ces.date         AS schedule_date,
                   ces.duration     AS schedule_duration,
                   ceoi.customer_id AS order_info_customer_id,
                   ceoi.car_id      AS order_info_car_id,
                   ceoi.description AS order_info_description,
                   ceoi.worker_id   AS order_info_worker_id,
                   ceo.order_id     AS order_id
            FROM calendar_entry e
                     LEFT JOIN calendar_entry_deletion ced on e.id = ced.entry_id
                     LEFT JOIN calendar_entry_order ceo ON ceo.entry_id = e.id
                     JOIN LATERAL (SELECT *
                                   FROM calendar_entry_schedule sub
                                   WHERE sub.entry_id = e.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) ces ON true
                     JOIN LATERAL (SELECT *
                                   FROM calendar_entry_order_info sub
                                   WHERE sub.entry_id = e.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) ceoi ON true
            WHERE ced IS NULL        
        ';
    }
}
