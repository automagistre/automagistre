<?php

namespace App\Calendar\Domain;

use App\Entity\Tenant\Employee;
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
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private ?Employee $worker;

    public function __construct(
        DateTimeImmutable $date,
        DateInterval $duration,
        ?Employee $worker,
        ?string $description
    ) {
        $this->id = CalendarEntryId::generate();
        $this->date = $date;
        $this->duration = $duration;
        $this->createdAt = new DateTimeImmutable();
        $this->worker = $worker;
        $this->description = $description;
    }

    public function id(): CalendarEntryId
    {
        return $this->id;
    }
}
