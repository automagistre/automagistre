<?php

declare(strict_types=1);

namespace App\Calendar\View;

use DateTimeImmutable;

interface Streamer
{
    public function byDate(DateTimeImmutable $date): StreamCollection;
}
