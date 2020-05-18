<?php

declare(strict_types=1);

namespace App\Calendar\Application\Create;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\OrderInfo;
use App\Calendar\Entity\Schedule;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class CreateCalendarEntryCommand
{
    public CalendarEntryId $id;

    /**
     * @Assert\Valid()
     */
    public Schedule $schedule;

    /**
     * @Assert\Valid()
     */
    public OrderInfo $orderInfo;

    public function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo)
    {
        $this->id = $id;
        $this->schedule = clone $schedule;
        $this->orderInfo = clone $orderInfo;
    }
}
