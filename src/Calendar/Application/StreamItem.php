<?php

namespace App\Calendar\Application;

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
