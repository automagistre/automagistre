<?php

namespace App\Calendar\Domain;

use App\Entity\Tenant\Employee;
use App\User\Domain\UserId;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $date;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private DateInterval $duration;

    /**
     * @ORM\Embedded(class=CalendarEntryCustomerInformation::class, columnPrefix=false)
     */
    private CalendarEntryCustomerInformation $customer;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="user_id")
     */
    private UserId $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private ?Employee $worker;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntry::class, cascade={"persist"})
     * @ORM\JoinColumn(name="previous")
     */
    private ?CalendarEntry $previous;

    /**
     * @ORM\OneToOne(targetEntity=CalendarEntryDeletion::class, mappedBy="entry", cascade={"persist"})
     */
    private ?CalendarEntryDeletion $deletion = null;

    private function __construct(
        DateTimeImmutable $date,
        DateInterval $duration,
        UserId $userId,
        CalendarEntryCustomerInformation $customer,
        ?Employee $worker,
        ?self $previous = null
    ) {
        $this->id = CalendarEntryId::generate();
        $this->date = $date;
        $this->duration = $duration;
        $this->createdAt = new DateTimeImmutable();
        $this->createdBy = $userId;
        $this->customer = $customer;
        $this->worker = $worker;
        $this->previous = $previous;
    }

    public function id(): CalendarEntryId
    {
        return $this->id;
    }

    public static function create(
        DateTimeImmutable $date,
        DateInterval $duration,
        CalendarEntryCustomerInformation $customer,
        UserId $userId,
        ?Employee $worker
    ): self {
        return new self($date, $duration, $userId, $customer, $worker);
    }

    public function reschedule(
        DateTimeImmutable $date,
        DateInterval $duration,
        CalendarEntryCustomerInformation $customer,
        UserId $userId,
        ?Employee $worker
    ): self {
        return new self($date, $duration, $userId, $customer, $worker, $this);
    }

    public function delete(DeletionReason $reason, ?string $description, UserId $deletedBy): CalendarEntryDeletion
    {
        return $this->deletion = new CalendarEntryDeletion($this, $reason, $description, $deletedBy);
    }
}
