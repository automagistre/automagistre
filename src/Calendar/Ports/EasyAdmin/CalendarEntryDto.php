<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Calendar\Domain\CalendarEntryId;
use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class CalendarEntryDto
{
    public ?CalendarEntryId $id;

    /**
     * @Assert\NotBlank()
     */
    public ?DateTimeImmutable $date;

    /**
     * @Assert\NotBlank()
     */
    public ?DateInterval $duration;

    public ?string $description;

    public ?Employee $worker;

    public function __construct(
        ?CalendarEntryId $id = null,
        ?DateTimeImmutable $date = null,
        ?DateInterval $duration = null,
        ?string $description = null,
        ?Employee $worker = null
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->duration = $duration;
        $this->worker = $worker;
        $this->description = $description;
    }
}
