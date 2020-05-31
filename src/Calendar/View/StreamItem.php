<?php

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;

final class StreamItem
{
    public int $length;

    public EntryView $calendar;

    public function __construct(int $length, EntryView $calendar)
    {
        $this->length = $length;
        $this->calendar = $calendar;
    }
}
