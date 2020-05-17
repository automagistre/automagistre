<?php

declare(strict_types=1);

namespace App\Calendar\Entity;

use App\Car\Entity\CarId;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Embeddable
 */
final class CalendarEntryCustomerInformation
{
    /**
     * @ORM\Column(length=32, nullable=true)
     */
    private ?string $firstName;

    /**
     * @ORM\Column(length=32, nullable=true)
     */
    private ?string $lastName;

    /**
     * @ORM\Column(type="phone_number", nullable=true)
     */
    private ?PhoneNumber $phone;

    /**
     * @ORM\Column(type="car_id", nullable=true)
     */
    private ?CarId $carId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?PhoneNumber $phone = null,
        ?CarId $carId = null,
        ?string $description = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->carId = $carId;
        $this->description = $description;
    }
}
