<?php

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
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
     * @ORM\Embedded(class=Schedule::class, columnPrefix=false)
     */
    private Schedule $schedule;

    /**
     * @ORM\Embedded(class=OrderInfo::class, columnPrefix=false)
     */
    private OrderInfo $orderInfo;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntry::class, mappedBy="previous", cascade={"persist"})
     */
    private ?CalendarEntry $replacement = null;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntry::class, inversedBy="replacement")
     * @ORM\JoinColumn(name="previous")
     */
    private ?CalendarEntry $previous;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntryDeletion::class, mappedBy="entry", cascade={"persist"})
     */
    private ?CalendarEntryDeletion $deletion = null;

    private function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo, ?self $previous = null)
    {
        $this->id = $id;
        $this->schedule = $schedule;
        $this->orderInfo = $orderInfo;
        $this->previous = $previous;
    }

    public static function create(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo): self
    {
        return new self($id, $schedule, $orderInfo);
    }

    public function reschedule(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo): void
    {
        if (null !== $this->replacement) {
            throw new DomainException(sprintf('%s %s already replaced.', __CLASS__, $this->id->toString()));
        }

        $this->replacement = new self($id, $schedule, $orderInfo, $this);
    }

    public function delete(DeletionReason $reason, ?string $description): void
    {
        if (null !== $this->deletion) {
            throw new DomainException(sprintf('%s %s already deleted.', __CLASS__, $this->id->toString()));
        }

        $this->deletion = new CalendarEntryDeletion($this, $reason, $description);
    }
}
