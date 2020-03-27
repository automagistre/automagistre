<?php

namespace App\Calendar\Application;

use App\Calendar\Domain\CalendarEntryId;
use App\Car\Entity\CarId;
use App\Entity\Tenant\Employee;
use DateInterval;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;

final class CalendarEntryView
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
        ?string $firstName = null,
        ?string $lastName = null,
        ?PhoneNumber $phone = null,
        ?CarId $carId = null,
        ?string $description = null,
        ?Employee $worker = null
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
