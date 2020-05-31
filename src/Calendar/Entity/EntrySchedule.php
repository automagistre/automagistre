<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar_entry_schedule")
 */
class EntrySchedule
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=CalendarEntry::class, inversedBy="schedules")
     */
    private CalendarEntry $entry;

    /**
     * @ORM\Embedded(class=Schedule::class, columnPrefix=false)
     */
    private Schedule $schedule;

    public function __construct(CalendarEntry $entry, Schedule $schedule)
    {
        $this->id = Uuid::uuid6();
        $this->entry = $entry;
        $this->schedule = $schedule;
    }

    public function equal(Schedule $schedule): bool
    {
        return $this->schedule->equal($schedule);
    }
}
