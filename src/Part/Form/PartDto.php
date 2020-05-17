<?php

namespace App\Part\Form;

use App\Manufacturer\Domain\ManufacturerId;
use App\Part\Domain\Part;
use App\Part\Domain\PartId;
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
     * @Assert\NotBlank
     * @Assert\Length(max="30")
     */
    public $number;

    /**
     * @var bool
     *
     * @Assert\Type("bool")
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
        ManufacturerId $manufacturerId,
        string $name,
        string $number,
        Money $price,
        bool $universal,
        Money $discount
    ) {
        $this->partId = $partId;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->number = $number;
        $this->price = $price;
        $this->universal = $universal;
        $this->discount = $discount;
    }
}
