<?php

namespace App\Part\Form;

use App\Manufacturer\Entity\Manufacturer;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class Part
{
    /**
     * @Assert\NotBlank()
     */
    public ?Manufacturer $manufacturer;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public ?string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="30")
     */
    public ?string $number;

    public bool $universal;

    public ?Money $price;

    public ?Money $discount;

    public function __construct(
        Manufacturer $manufacturer = null,
        string $name = null,
        string $number = null,
        Money $price = null,
        bool $universal = false,
        Money $discount = null
    ) {
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->number = $number;
        $this->price = $price;
        $this->universal = $universal;
        $this->discount = $discount;
    }
}
