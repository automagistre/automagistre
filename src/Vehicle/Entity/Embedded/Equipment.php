<?php

declare(strict_types=1);

namespace App\Vehicle\Entity\Embedded;

use App\Vehicle\Enum\DriveWheelConfiguration;
use App\Vehicle\Enum\Transmission;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function sprintf;

/**
 * @ORM\Embeddable
 */
final class Equipment
{
    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class=Engine::class)
     */
    public Engine $engine;

    /**
     * @ORM\Column(type="car_transmission_enum")
     */
    public Transmission $transmission;

    /**
     * @ORM\Column(type="car_wheel_drive_enum")
     */
    public DriveWheelConfiguration $wheelDrive;

    public function __construct(
        Engine $engine = null,
        Transmission $transmission = null,
        DriveWheelConfiguration $wheelDrive = null,
    ) {
        $this->engine = $engine ?? new Engine();
        $this->wheelDrive = $wheelDrive ?? DriveWheelConfiguration::unknown();
        $this->transmission = $transmission ?? Transmission::unknown();
    }

    public function isFilled(): bool
    {
        return
            !$this->wheelDrive->eq(DriveWheelConfiguration::unknown())
            && !$this->transmission->eq(Transmission::unknown())
            && $this->engine->isFilled();
    }

    public function toString(): string
    {
        return sprintf(
            '%s %s %s',
            $this->engine->toString(),
            $this->transmission->toCode(),
            $this->wheelDrive->toCode(),
        );
    }
}
