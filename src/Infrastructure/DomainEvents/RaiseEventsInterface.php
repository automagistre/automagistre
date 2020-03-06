<?php

declare(strict_types=1);

namespace App\Infrastructure\DomainEvents;

use Symfony\Contracts\EventDispatcher\Event;

interface RaiseEventsInterface
{
    /**
     * @return Event[]
     */
    public function popEvents(): array;
}
