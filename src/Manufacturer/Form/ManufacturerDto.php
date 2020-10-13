<?php

declare(strict_types=1);

namespace App\Manufacturer\Form;

use App\Manufacturer\Entity\Manufacturer;
use App\Shared\Validator\EntityCheck;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityCheck(
 *     class=Manufacturer::class,
 *     message="Производитель с такими названием уже существует",
 *     fields={"name": "name"},
 *     exists=false,
 *     errorPath="name",
 * )
 *
 * @psalm-suppress MissingConstructor
 */
final class ManufacturerDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var string
     */
    public $localizedName;
}
