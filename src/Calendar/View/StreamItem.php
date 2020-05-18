<?php

namespace App\Calendar\View;

use App\Calendar\Form\CalendarEntryDto;

final class StreamItem
{
    public int $length;

    public CalendarEntryDto $calendar;

    public function __construct(int $length, CalendarEntryDto $calendar)
    {
        $this->length = $length;
        $this->calendar = $calendar;
    }
}
