<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Manufacturer\Entity\ManufacturerId;
use App\Shared\Validator\EntityCheck;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityCheck(
 *     class=Model::class,
 *     message="Такой кузов уже существует",
 *     fields={"manufacturerId": "manufacturerId", "name": "name", "caseName": "caseName"},
 *     exists=false,
 *     errorPath="name",
 * )
 */
final class ModelDto
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

    public function __construct(
        VehicleId $vehicleId,
        ManufacturerId $manufacturerId,
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill
    ) {
        $this->vehicleId = $vehicleId;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->caseName = $caseName;
        $this->yearFrom = $yearFrom;
        $this->yearTill = $yearTill;
    }
}
