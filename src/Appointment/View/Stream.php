<?php

namespace App\Appointment\View;

use App\Appointment\Entity\Appointment;
use App\Entity\Tenant\Employee;
use function array_intersect;
use function array_keys;
use DateInterval;

final class Stream
{
    public ?Employee $worker;

    /**
     * @var StreamItem[]
     */
    private array $items = [];

    /**
     * @param Appointment[] $appointments
     */
    public function __construct(Employee $worker = null, array $appointments = [])
    {
        $this->worker = $worker;
        foreach ($appointments as $appointment) {
            $this->add($appointment);
        }
    }

    public function add(Appointment $appointment): void
    {
        $date = $appointment->date;
        $interval = $appointment->duration;

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

        $item = new StreamItem($length, $appointment);
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
