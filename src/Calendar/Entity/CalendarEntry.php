<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use App\Calendar\Exception\EntryDeleted;
use App\Tenant\Entity\TenantEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CalendarEntry extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private CalendarEntryId $id;

    /**
     * @var Collection<int, EntrySchedule>
     *
     * @ORM\OneToMany(targetEntity=EntrySchedule::class, mappedBy="entry", cascade={"persist"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private Collection $schedules;

    /**
     * @var Collection<int, EntryOrderInfo>
     *
     * @ORM\OneToMany(targetEntity=EntryOrderInfo::class, mappedBy="entry", cascade={"persist"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private Collection $orders;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntryDeletion::class, mappedBy="entry", cascade={"persist"})
     */
    private ?CalendarEntryDeletion $deletion = null;

    private function __construct(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo)
    {
        $this->id = $id;
        $this->schedules = new ArrayCollection();
        $this->schedules[] = new EntrySchedule($this, $schedule);
        $this->orders = new ArrayCollection();
        $this->orders[] = new EntryOrderInfo($this, $orderInfo);
    }

    public function toId(): CalendarEntryId
    {
        return $this->id;
    }

    public static function create(CalendarEntryId $id, Schedule $schedule, OrderInfo $orderInfo): self
    {
        return new self($id, $schedule, $orderInfo);
    }

    public function reschedule(Schedule $schedule): void
    {
        $this->failIfClosed();

        $last = $this->schedules->last();

        if (false !== $last && $last->equal($schedule)) {
            return;
        }

        $this->schedules[] = new EntrySchedule($this, $schedule);
    }

    public function changeOrderInfo(OrderInfo $orderInfo): void
    {
        $this->failIfClosed();

        $last = $this->orders->last();

        if (false !== $last && $last->equal($orderInfo)) {
            return;
        }

        $this->orders[] = new EntryOrderInfo($this, $orderInfo);
    }

    public function delete(DeletionReason $reason, ?string $description): void
    {
        $this->failIfClosed();

        $this->deletion = new CalendarEntryDeletion($this, $reason, $description);
    }

    private function failIfClosed(): void
    {
        if (null === $this->deletion) {
            return;
        }

        throw EntryDeleted::fromEntryId($this->id);
    }
}
