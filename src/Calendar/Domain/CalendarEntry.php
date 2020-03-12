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
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="user_id")
     */
    private UserId $createdBy;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private ?Employee $worker;

    /**
     * @ORM\Column(type="calendar_entry_id", nullable=true)
     */
    private ?CalendarEntryId $previous;

    public function __construct(
        DateTimeImmutable $date,
        DateInterval $duration,
        UserId $userId,
        ?Employee $worker,
        ?string $description,
        CalendarEntryId $previous = null
    ) {
        $this->id = CalendarEntryId::generate();
        $this->date = $date;
        $this->duration = $duration;
        $this->createdAt = new DateTimeImmutable();
        $this->createdBy = $userId;
        $this->worker = $worker;
        $this->description = $description;
        $this->previous = $previous;
    }

    public function id(): CalendarEntryId
    {
        return $this->id;
    }
}
