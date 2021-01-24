<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Manufacturer\Entity\ManufacturerId;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Enum\Unit;
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
 *
 * @psalm-suppress MissingConstructor
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
     * @var Unit
     *
     * @Assert\NotBlank
     */
    public $unit;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var Money
     */
    public $discount;
}
