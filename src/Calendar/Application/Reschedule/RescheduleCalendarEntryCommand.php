<?php

declare(strict_types=1);

namespace App\Calendar\Application\Reschedule;

use App\Calendar\Entity\CalendarEntryId;
use App\Car\Entity\CarId;
use App\Employee\Entity\Employee;
use DateInterval;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;

final class RescheduleCalendarEntryCommand
{
    public CalendarEntryId $id;

    public DateTimeImmutable $date;

    public DateInterval $duration;

    public ?string $firstName;

    public ?string $lastName;

    public ?PhoneNumber $phone;

    public ?CarId $carId;

    public ?string $description;

    public ?Employee $worker;

    public function __construct(
        CalendarEntryId $id,
        DateTimeImmutable $date,
        DateInterval $duration,
        ?string $firstName,
        ?string $lastName,
        ?PhoneNumber $phone,
        ?CarId $carId,
        ?string $description,
        ?Employee $worker
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->duration = $duration;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->carId = $carId;
        $this->description = $description;
        $this->worker = $worker;
    }
}
