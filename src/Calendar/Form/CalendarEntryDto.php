<?php

namespace App\Calendar\Form;

use App\Calendar\Entity\CalendarEntryId;
use App\Car\Entity\CarId;
use App\Employee\Entity\Employee;
use DateInterval;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    /**
     * @Assert\Length(max="32")
     */
    public ?string $firstName;

    /**
     * @Assert\Length(max="32")
     */
    public ?string $lastName;

    public ?PhoneNumber $phone;

    public ?CarId $carId;

    public ?string $description;

    public ?Employee $worker;

    public function __construct(
        ?CalendarEntryId $id = null,
        ?DateTimeImmutable $date = null,
        ?DateInterval $duration = null,
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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (
            null === $this->description
            && null === $this->phone
            && null === $this->firstName
            && null === $this->lastName
        ) {
            $context->addViolation(
                'Нужно заполнить хотя бы одно из следующий полей: Телефон, Имя, Фамилия, Комментарий.'
            );
        }
    }
}
