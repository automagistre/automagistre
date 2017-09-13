<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\Carcase;
use App\Entity\Enum\CarTransmission;
use App\Entity\Enum\CarWheelDrive;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarModification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var int
     *
     * @Assert\Type("int")
     *
     * @ORM\Column(name="`case`", type="smallint", nullable=true)
     */
    private $case;

    /**
     * @var string
     *
     * @ORM\Column(name="`engine`", nullable=true)
     */
    private $engine;

    /**
     * @var int
     *
     * @Assert\Type("int")
     *
     * @ORM\Column(name="hp", type="smallint", nullable=true)
     */
    private $hp;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $transmission;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $wheelDrive;

    /**
     * @var int
     *
     * @ORM\Column(name="doors", type="smallint", nullable=true)
     */
    private $doors;

    /**
     * @var int
     *
     * @ORM\Column(name="from", type="smallint", nullable=true)
     */
    private $from;

    /**
     * @var int
     *
     * @ORM\Column(name="till", type="smallint", nullable=true)
     */
    private $till;

    /**
     * @var string
     *
     * @ORM\Column(name="maxspeed", length=20, nullable=true)
     */
    private $maxspeed;

    /**
     * @var string
     *
     * @ORM\Column(name="s0to100", length=20, nullable=true)
     */
    private $s0to100;

    /**
     * @var int
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
            $case ? $case->getName() : ''
        );
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCase(): ?Carcase
    {
        return $this->case ? new Carcase($this->case) : null;
    }

    public function setCase(Carcase $case = null): void
    {
        $this->case = $case ? $case->getId() : $case;
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

    public function setEngine(string $engine): void
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
        return $this->transmission ? new CarTransmission($this->transmission) : null;
    }

    public function setTransmission(CarTransmission $transmission = null): void
    {
        $this->transmission = $transmission ? $transmission->getId() : $transmission;
    }

    public function getWheelDrive(): ?CarWheelDrive
    {
        return $this->wheelDrive ? new CarWheelDrive($this->wheelDrive) : null;
    }

    public function setWheelDrive(CarWheelDrive $wheelDrive = null): void
    {
        $this->wheelDrive = $wheelDrive ? $wheelDrive->getId() : $wheelDrive;
    }

    public function getDoors(): ?int
    {
        return $this->doors;
    }

    public function setDoors(int $doors): void
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

    public function setTill(int $till): void
    {
        $this->till = $till;
    }

    public function getMaxspeed(): ?string
    {
        return $this->maxspeed;
    }

    public function setMaxspeed(string $maxspeed): void
    {
        $this->maxspeed = $maxspeed;
    }

    public function getS0to100(): ?string
    {
        return $this->s0to100;
    }

    public function setS0to100(string $s0to100): void
    {
        $this->s0to100 = $s0to100;
    }

    public function getTank(): ?int
    {
        return $this->tank;
    }

    public function setTank(int $tank): void
    {
        $this->tank = $tank;
    }

    public function getDisplayName(): string
    {
        $transmission = $this->getTransmission();
        $wheelDrive = $this->getWheelDrive();

        return sprintf(
            '%s %s',
            $this->getCarGeneration()->getDisplayName(),
            $this->getEngine() ? sprintf(
                '%s (%s) %s %s',
                $this->getEngine(),
                $this->getHp(),
                $transmission ? $transmission->getCode() : '',
                $wheelDrive ? $wheelDrive->getCode() : ''
            ) : $this->getName()
        );
    }
}
