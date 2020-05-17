<?php

namespace App\Calendar\View;

use DateTimeImmutable;

interface Streamer
{
    public function byDate(DateTimeImmutable $date): StreamCollection;
}
