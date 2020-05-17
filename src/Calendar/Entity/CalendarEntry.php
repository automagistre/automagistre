<?php

namespace App\Calendar\Entity;

use App\Calendar\Enum\DeletionReason;
use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;
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
     * @ORM\Embedded(class=CalendarEntryCustomerInformation::class, columnPrefix=false)
     */
    private CalendarEntryCustomerInformation $customer;

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
        CalendarEntryCustomerInformation $customer,
        ?Employee $worker,
        ?self $previous = null
    ) {
        $this->id = CalendarEntryId::generate();
        $this->schedule = new Schedule($date, $duration);
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
        ?Employee $worker
    ): self {
        return new self($date, $duration, $customer, $worker);
    }

    public function reschedule(
        DateTimeImmutable $date,
        DateInterval $duration,
        CalendarEntryCustomerInformation $customer,
        ?Employee $worker
    ): self {
        return new self($date, $duration, $customer, $worker, $this);
    }

    public function delete(DeletionReason $reason, ?string $description): void
    {
        if (null !== $this->deletion) {
            throw new DomainException(sprintf('%s %s already deleted.', __CLASS__, $this->id->toString()));
        }

        $this->deletion = new CalendarEntryDeletion($this, $reason, $description);
    }
}
