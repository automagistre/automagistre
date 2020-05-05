<?php

namespace App\Part\Form;

use App\Manufacturer\Domain\Manufacturer;
use App\Part\Domain\PartId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

final class PartDto
{
    public PartId $partId;

    /**
     * @var Manufacturer
     *
     * @Assert\NotBlank
     */
    public $manufacturer;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max="30")
     */
    public $number;

    /**
     * @var bool
     *
     * @Assert\NotBlank
     */
    public $universal;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $price;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $discount;

    public function __construct(
        PartId $partId,
        Manufacturer $manufacturer,
        string $name,
        string $number,
        Money $price,
        bool $universal,
        Money $discount
    ) {
        $this->partId = $partId;
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->number = $number;
        $this->price = $price;
        $this->universal = $universal;
        $this->discount = $discount;
    }
}
