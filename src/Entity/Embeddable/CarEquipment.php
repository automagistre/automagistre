<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use Doctrine\ORM\Mapping as ORM;
use function sprintf;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
final class CarEquipment
{
    /**
     * @var CarEngine
     *
     * @Assert\Valid
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

    public function isFilled(): bool
    {
        return
            !$this->wheelDrive->eq(CarWheelDrive::unknown())
            && !$this->transmission->eq(CarTransmission::unknown())
            && $this->engine->isFilled();
    }

    public function toString(): string
    {
        return sprintf(
            '%s %s %s',
            $this->engine->toString(),
            $this->transmission->getCode(),
            $this->wheelDrive->getCode()
        );
    }
}
