<?php

namespace App\Part\Form;

use App\Manufacturer\Entity\ManufacturerId;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Shared\Validator\EntityCheck;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityCheck(
 *     class=Part::class,
 *     message="Запчасть с такими идентификатором уже существует",
 *     fields={"manufacturerId": "manufacturerId", "number": "number"},
 *     exists=false,
 *     errorPath="number",
 * )
 */
final class PartDto
{
    public PartId $partId;

    /**
     * @var ManufacturerId
     *
     * @Assert\NotBlank
     */
    public $manufacturerId;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max="30")
     */
    public $number;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
     */
    public $universal = false;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var Money
     */
    public $discount;

    public function __construct(
        PartId $partId,
        ManufacturerId $manufacturerId,
        string $name,
        string $number,
        bool $universal,
        Money $price,
        Money $discount
    ) {
        $this->partId = $partId;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->number = $number;
        $this->universal = $universal;
        $this->price = $price;
        $this->discount = $discount;
    }
}
