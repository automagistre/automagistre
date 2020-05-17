<?php

namespace App\Calendar\View;

final class StreamItem
{
    public int $length;

    public CalendarEntryView $calendar;

    public function __construct(int $length, CalendarEntryView $calendar)
    {
        $this->length = $length;
        $this->calendar = $calendar;
    }
}
