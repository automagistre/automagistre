<?php

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use function sprintf;

/**
 * @ORM\Entity
 */
class CalendarEntry
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="calendar_entry_id")
     */
    private CalendarEntryId $id;

    /**
     * @var Collection<int, EntrySchedule>
     *
     * @ORM\OneToMany(targetEntity=EntrySchedule::class, mappedBy="entry", cascade={"persist"})
     * @ORM\OrderBy({"id": "DESC"})
     */
    private Collection $schedules;

    /**
     * @var Collection<int, EntryOrder>
     *
     * @ORM\OneToMany(targetEntity=EntryOrder::class, mappedBy="entry", cascade={"persist"})
     * @ORM\OrderBy({"id": "DESC"})
     */
    private Collection $orders;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntryDeletion::class, mappedBy="entry", cascade={"persist"})
     */
    private ?CalendarEntryDeletion $deletion = null;

    private function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo)
    {
        $this->id = $id;
        $this->schedules = new ArrayCollection([new EntrySchedule($this, $schedule)]);
        $this->orders = new ArrayCollection([new EntryOrder($this, $orderInfo)]);
    }

    public static function create(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo): self
    {
        return new self($id, $schedule, $orderInfo);
    }

    public function reschedule(Schedule $schedule): void
    {
        $last = $this->schedules->last();
        if (false !== $last && $last->equal($schedule)) {
            return;
        }

        $this->schedules[] = new EntrySchedule($this, $schedule);
    }

    public function changeOrderInfo(OrderInfo $orderInfo): void
    {
        $last = $this->orders->last();
        if (false !== $last && $last->equal($orderInfo)) {
            return;
        }

        $this->orders[] = new EntryOrder($this, $orderInfo);
    }

    public function delete(DeletionReason $reason, ?string $description): void
    {
        if (null !== $this->deletion) {
            throw new DomainException(sprintf('%s %s already deleted.', __CLASS__, $this->id->toString()));
        }

        $this->deletion = new CalendarEntryDeletion($this, $reason, $description);
    }
}
