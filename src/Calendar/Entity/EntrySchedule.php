<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Calendar\Event\EntryScheduled;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="calendar_entry_schedule")
 */
class EntrySchedule implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
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

        $this->record(new EntryScheduled($entry->toId()));
    }

    public function equal(Schedule $schedule): bool
    {
        return $this->schedule->equal($schedule);
    }
}
