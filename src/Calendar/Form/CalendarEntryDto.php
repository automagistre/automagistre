<?php

namespace App\Calendar\Form;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryView;
use Symfony\Component\Validator\Constraints as Assert;

final class CalendarEntryDto
{
    public CalendarEntryId $id;

    /**
     * @Assert\Valid())
     */
    public ScheduleDto $schedule;

    /**
     * @Assert\Valid()
     */
    public OrderInfoDto $orderInfo;

    public function __construct(CalendarEntryId $id, ScheduleDto $schedule, OrderInfoDto $orderInfo)
    {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->orderInfo = $orderInfo;
    }

    public static function fromView(EntryView $view): self
    {
        return new self(
            $view->id,
            ScheduleDto::fromSchedule($view->schedule),
            OrderInfoDto::fromOrderInfo($view->orderInfo),
        );
    }
}
