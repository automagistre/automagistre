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
}
