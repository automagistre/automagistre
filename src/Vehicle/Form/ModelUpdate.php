<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Manufacturer\Entity\ManufacturerId;
use App\Vehicle\Entity\VehicleId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class ModelUpdate
{
    public VehicleId $vehicleId;

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
     * @var string|null
     */
    public $localizedName;

    /**
     * @var string|null
     */
    public $caseName;

    /**
     * @var int|null
     */
    public $yearFrom;

    /**
     * @var int|null
     */
    public $yearTill;
}
