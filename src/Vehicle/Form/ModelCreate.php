<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Manufacturer\Entity\ManufacturerId;
use App\Shared\Validator\EntityCheck;
use App\Vehicle\Entity\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityCheck(
 *     class=Model::class,
 *     message="Такой кузов уже существует",
 *     fields={"manufacturerId": "manufacturerId", "name": "name", "caseName": "caseName"},
 *     exists=false,
 *     errorPath="name",
 * )
 *
 * @psalm-suppress MissingConstructor
 */
final class ModelCreate
{
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
     */
    public $name;

    /**
     * @var null|string
     */
    public $localizedName;

    /**
     * @var null|string
     */
    public $caseName;

    /**
     * @var null|int
     */
    public $yearFrom;

    /**
     * @var null|int
     */
    public $yearTill;
}
