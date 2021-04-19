<?php

declare(strict_types=1);

namespace App\Note\Entity;

interface Notes
{
    /**
     * @return iterable<int, array>
     */
    public function notes(): iterable;
}
