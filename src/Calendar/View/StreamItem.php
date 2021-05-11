<?php

declare(strict_types=1);

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;

final class StreamItem
{
    public function __construct(public int $length, public EntryView $calendar)
    {
    }
}
