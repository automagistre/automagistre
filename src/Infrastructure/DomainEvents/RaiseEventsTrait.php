<?php

declare(strict_types=1);

namespace App\Infrastructure\DomainEvents;

use Symfony\Contracts\EventDispatcher\Event;

trait RaiseEventsTrait
{
    /**
     * @var array<int, Event>
     */
    protected array $events = [];

    /**
     * @return array<int, Event>
     */
    public function popEvents(): array
    {
        [$events, $this->events] = [$this->events, []];

        return $events;
    }

    protected function raise(Event $event): void
    {
        $this->events[] = $event;
    }
}
