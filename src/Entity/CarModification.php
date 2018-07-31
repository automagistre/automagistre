<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\Carcase;
use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarModification
{
    use Identity;

    /**
     * @var CarGeneration
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarGeneration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carGeneration;

    /**
     * @var string
     *
     * @ORM\Column(name="name", length=30, nullable=true)
     */
    private $name;

    /**
     * @var Carcase|null
     *
     * @ORM\Column(name="`case`", type="carcase_enum", nullable=true)
     */
    private $case;

    /**
     * @var string|null
     *
     * @ORM\Column(name="`engine`", nullable=true)
     */
    private $engine;

    /**
     * @var int|null
     *
     * @ORM\Column(name="hp", type="smallint", nullable=true)
     */
    private $hp;

    /**
     * @var CarTransmission|null
     *
     * @ORM\Column(type="car_transmission_enum", nullable=true)
     */
    private $transmission;

    /**
     * @var CarWheelDrive|null
     *
     * @ORM\Column(type="car_wheel_drive_enum", nullable=true)
     */
    private $wheelDrive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="doors", type="smallint", nullable=true)
     */
    private $doors;

    /**
     * @var int|null
     *
     * @ORM\Column(name="from", type="smallint", nullable=true)
     */
    private $from;

    /**
     * @var int|null
     *
     * @ORM\Column(name="till", type="smallint", nullable=true)
     */
    private $till;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maxspeed", length=20, nullable=true)
     */
    private $maxspeed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="s0to100", length=20, nullable=true)
     */
    private $s0to100;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tank", type="smallint", nullable=true)
     */
    private $tank;

    public function __toString(): string
    {
        $case = $this->getCase();

        return sprintf(
            '%s (%s-%s) %s',
            $this->getDisplayName(),
            $this->getFrom(),
            $this->getTill() ?: '...',
            $case instanceof Carcase ? $case->getName() : ''
        );
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCase(): ?Carcase
    {
        return $this->case;
    }

    public function setCase(Carcase $case): void
    {
        $this->case = $case;
    }

    public function getCarGeneration(): ?CarGeneration
    {
        return $this->carGeneration;
    }

    public function setCarGeneration(CarGeneration $carGeneration): void
    {
        $this->carGeneration = $carGeneration;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function setEngine(?string $engine): void
    {
        $this->engine = $engine;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(int $hp): void
    {
        $this->hp = $hp;
    }

    public function getTransmission(): ?CarTransmission
    {
        return $this->transmission;
    }

    public function setTransmission(CarTransmission $transmission): void
    {
        $this->transmission = $transmission;
    }

    public function getWheelDrive(): ?CarWheelDrive
    {
        return $this->wheelDrive;
    }

    public function setWheelDrive(CarWheelDrive $wheelDrive): void
    {
        $this->wheelDrive = $wheelDrive;
    }

    public function getDoors(): ?int
    {
        return $this->doors;
    }

    public function setDoors(?int $doors): void
    {
        $this->doors = $doors;
    }

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    public function getTill(): ?int
    {
        return $this->till;
    }

    public function setTill(?int $till): void
    {
        $this->till = $till;
    }

    public function getMaxspeed(): ?string
    {
        return $this->maxspeed;
    }

    public function setMaxspeed(?string $maxspeed): void
    {
        $this->maxspeed = $maxspeed;
    }

    public function getS0to100(): ?string
    {
        return $this->s0to100;
    }

    public function setS0to100(?string $s0to100): void
    {
        $this->s0to100 = $s0to100;
    }

    public function getTank(): ?int
    {
        return $this->tank;
    }

    public function setTank(?int $tank): void
    {
        $this->tank = $tank;
    }

    public function getDisplayName(): string
    {
        $transmission = $this->getTransmission();
        $wheelDrive = $this->getWheelDrive();

        $engine = $this->getEngine();

        return sprintf(
            '%s %s (%s - %s)',
            $this->getCarGeneration()->getDisplayName(),
            null === $engine
                ? $this->getName()
                : sprintf(
                '%s (%s) %s %s',
                $engine,
                $this->getHp() ?? '-',
                null !== $transmission ? $transmission->{'getCode'}() : '-', // TODO phpstan don't see return type
                null !== $wheelDrive ? $wheelDrive->{'getCode'}() : '-'
            ),
            $this->from ?? '...',
            $this->till ?? '...'
        );
    }
}
