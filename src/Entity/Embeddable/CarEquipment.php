<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class CarEquipment
{
    /**
     * @var CarEngine
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\CarEngine")
     */
    public $engine;

    /**
     * @var CarTransmission
     *
     * @ORM\Column(type="car_transmission_enum")
     */
    public $transmission;

    /**
     * @var CarWheelDrive
     *
     * @ORM\Column(type="car_wheel_drive_enum")
     */
    public $wheelDrive;

    public function __construct(
        CarEngine $engine = null,
        CarTransmission $transmission = null,
        CarWheelDrive $wheelDrive = null
    ) {
        $this->engine = $engine ?? new CarEngine();
        $this->wheelDrive = $wheelDrive ?? CarWheelDrive::unknown();
        $this->transmission = $transmission ?? CarTransmission::unknown();
    }
}
