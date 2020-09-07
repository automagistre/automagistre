<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Car;
use App\Shared\Validator\EntityCheck;
use App\Vehicle\Entity\Embedded\Equipment;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Enum\BodyType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityCheck(
 *     class=Car::class,
 *     message="Автомобиль с такими идентификатором уже существует",
 *     fields={"identifier": "identifier"},
 *     exists=false,
 *     errorPath="identifier",
 * )
 */
final class CarCreate
{
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
     * @var string|null
     *
     * @Assert\Length(max="17")
     */
    public $identifier;

    /**
     * @var int|null
     */
    public $year;

    /**
     * @var BodyType
     *
     * @Assert\Valid
     */
    public $caseType;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $gosnomer;

    private function __construct(
        VehicleId $vehicleId,
        Equipment $equipment = null,
        string $identifier = null,
        ?int $year = null,
        BodyType $caseType = null,
        string $description = null,
        string $gosnomer = null
    ) {
        $this->vehicleId = $vehicleId;
        $this->equipment = $equipment ?? new Equipment();
        $this->identifier = $identifier;
        $this->year = $year;
        $this->caseType = $caseType ?? BodyType::unknown();
        $this->description = $description;
        $this->gosnomer = $gosnomer;
    }
}
