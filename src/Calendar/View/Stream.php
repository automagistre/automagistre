<?php

namespace App\Calendar\View;

use App\Calendar\Entity\EntryView;
use App\Employee\Entity\EmployeeId;
use function array_intersect;
use function array_keys;
use DateInterval;

final class Stream
{
    public ?EmployeeId $workerId;

    /**
     * @var StreamItem[]
     */
    private array $items = [];

    /**
     * @param EntryView[] $calendars
     */
    public function __construct(EmployeeId $worker = null, array $calendars = [])
    {
        $this->workerId = $worker;
        foreach ($calendars as $calendar) {
            $this->add($calendar);
        }
    }

    public function add(EntryView $calendar): void
    {
        $date = $calendar->schedule->date;
        $interval = $calendar->schedule->duration;

        $key = $date->format('H:i');
        /** @psalm-suppress PossiblyNullPropertyFetch */
        $length = (int) ($interval->h * 2 + $interval->i / 30);

        $keys = [$key];
        for ($i = 0; $i < $length - 1; ++$i) {
            $date = $date->add(new DateInterval('PT30M'));
            $keys[] = $date->format('H:i');
        }

        $definedKeys = array_intersect($keys, array_keys($this->items));
        if ([] !== $definedKeys) {
            throw StreamOverflowException::fromKeys($definedKeys);
        }

        $item = new StreamItem($length, $calendar);
        foreach ($keys as $k) {
            $this->items[$k] = $item;
        }
    }

    public function has(string $key): bool
    {
        return ($this->items[$key] ?? null) instanceof StreamItem;
    }

    public function get(string $key): StreamItem
    {
        return $this->items[$key];
    }

    /**
     * @return StreamItem[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
