<?php

namespace App\Calendar\Application;

use DateTimeImmutable;

interface Streamer
{
    public function byDate(DateTimeImmutable $date): StreamCollection;
}
