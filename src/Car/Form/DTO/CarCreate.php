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
 *
 * @psalm-suppress MissingConstructor
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
}
