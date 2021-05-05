<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\CarId;
use App\Vehicle\Entity\Embedded\Equipment;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\BodyType;
use Symfony\Component\Validator\Constraints as Assert;

final class CarUpdate
{
    public CarId $carId;

    /**
     * @var Equipment
     *
     * @Assert\Valid
     */
    public $equipment;

    /**
     * @var VehicleId
     *
     * @Assert\NotBlank
     */
    public $vehicleId;

    /**
     * @var null|string
     *
     * @Assert\Length(max="17")
     */
    public $identifier;

    /**
     * @var null|int
     */
    public $year;

    /**
     * @var BodyType
     *
     * @Assert\Valid
     */
    public $caseType;

    /**
     * @var null|string
     */
    public $description;

    /**
     * @var null|string
     */
    public $gosnomer;

    public function __construct(
        CarId $carId,
        VehicleId $vehicleId,
        Equipment $equipment = null,
        string $identifier = null,
        ?int $year = null,
        BodyType $caseType = null,
        string $description = null,
        string $gosnomer = null,
    ) {
        $this->carId = $carId;
        $this->vehicleId = $vehicleId;
        $this->equipment = $equipment ?? new Equipment();
        $this->identifier = $identifier;
        $this->year = $year;
        $this->caseType = $caseType ?? BodyType::unknown();
        $this->description = $description;
        $this->gosnomer = $gosnomer;
    }
}
