<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Manufacturer\Entity\Manufacturer;
use App\Vehicle\Entity\VehicleId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ModelDto
{
    public VehicleId $vehicleId;

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

    public function __construct(
        VehicleId $vehicleId,
        Manufacturer $manufacturer,
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill
    ) {
        $this->vehicleId = $vehicleId;
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->caseName = $caseName;
        $this->yearFrom = $yearFrom;
        $this->yearTill = $yearTill;
    }
}
