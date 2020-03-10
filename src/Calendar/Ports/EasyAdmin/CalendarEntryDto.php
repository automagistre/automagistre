<?php

namespace App\Calendar\Ports\EasyAdmin;

use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class CalendarEntryDto
{
    /**
     * @Assert\NotBlank()
     */
    public ?DateTimeImmutable $date = null;

    /**
     * @Assert\NotBlank()
     */
    public ?DateInterval $duration = null;

    public ?Employee $worker = null;

    public ?string $description = null;
}
