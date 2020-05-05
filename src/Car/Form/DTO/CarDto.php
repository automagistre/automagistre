<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\CarId;
use App\Vehicle\Domain\BodyType;
use App\Vehicle\Domain\Equipment;
use App\Vehicle\Domain\Model;
use Symfony\Component\Validator\Constraints as Assert;

final class CarDto
{
    public CarId $carId;

    /**
     * @var Equipment
     *
     * @Assert\Valid
     */
    public $equipment;

    /**
     * @var Model|null
     *
     * @Assert\NotBlank
     */
    public $model;

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

    public function __construct(
        CarId $carId,
        Equipment $equipment = null,
        Model $model = null,
        string $identifier = null,
        ?int $year = null,
        BodyType $caseType = null,
        string $description = null,
        string $gosnomer = null
    ) {
        $this->carId = $carId;
        $this->equipment = $equipment ?? new Equipment();
        $this->model = $model;
        $this->identifier = $identifier;
        $this->year = $year;
        $this->caseType = $caseType ?? BodyType::unknown();
        $this->description = $description;
        $this->gosnomer = $gosnomer;
    }
}
