<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\Carcase;
use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use App\Enum\EngineType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"vin"})
 */
class Car
{
    use Identity;
    use CreatedAt;

    /**
     * @var CarModel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarModel")
     * @ORM\JoinColumn
     */
    private $carModel;

    /**
     * @var EngineType
     *
     * @ORM\Column(type="engine_type_enum")
     */
    private $engineType;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $engineCapacity;

    /**
     * @var CarTransmission
     *
     * @ORM\Column(type="car_transmission_enum")
     */
    private $transmission;

    /**
     * @var CarWheelDrive
     *
     * @ORM\Column(type="car_wheel_drive_enum")
     */
    private $wheelDrive;

    /**
     * @var string
     *
     * @ORM\Column(length=17, nullable=true, unique=true)
     */
    private $vin;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @var Carcase
     *
     * @ORM\Column(type="carcase_enum")
     */
    private $caseType;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $owner;

    /**
     * license plate.
     *
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $gosnomer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var Order[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="car")
     */
    private $orders;

    /**
     * @var CarRecommendation[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CarRecommendation", mappedBy="car", cascade={"persist"})
     */
    private $recommendations;

    public function __construct()
    {
        $this->engineType = EngineType::unknown();
        $this->wheelDrive = CarWheelDrive::unknown();
        $this->transmission = CarTransmission::unknown();
        $this->caseType = Carcase::unknown();
        $this->orders = new ArrayCollection();
        $this->recommendations = new ArrayCollection();
    }

    public function __toString(): string
    {
        $string = $this->getCarModificationDisplayName();
        $gosnomer = $this->getGosnomer();

        if (null !== $gosnomer) {
            $string .= \sprintf(', (%s)', $gosnomer);
        }

        return $string;
    }

    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    public function setCarModel(CarModel $carModel): void
    {
        $this->carModel = $carModel;
    }

    public function getEngineType(): EngineType
    {
        return $this->engineType;
    }

    public function setEngineType(EngineType $engineType): void
    {
        $this->engineType = $engineType;
    }

    public function getEngineCapacity(): ?string
    {
        return $this->engineCapacity;
    }

    public function setEngineCapacity(?string $engineCapacity): void
    {
        $this->engineCapacity = $engineCapacity;
    }

    public function getTransmission(): CarTransmission
    {
        return $this->transmission;
    }

    public function setTransmission(CarTransmission $transmission): void
    {
        $this->transmission = $transmission;
    }

    public function getWheelDrive(): CarWheelDrive
    {
        return $this->wheelDrive;
    }

    public function setWheelDrive(CarWheelDrive $wheelDrive): void
    {
        $this->wheelDrive = $wheelDrive;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    /**
     * @param string $vin
     */
    public function setVin(string $vin = null): void
    {
        $this->vin = $vin;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getCaseType(): Carcase
    {
        return $this->caseType;
    }

    public function setCaseType(Carcase $caseType): void
    {
        $this->caseType = $caseType;
    }

    public function getOwner(): ?Operand
    {
        return $this->owner;
    }

    public function setOwner(Operand $owner): void
    {
        $this->owner = $owner;
    }

    public function getMileage(): ?int
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->neq('mileage', null));
        $criteria->orderBy(['mileage' => 'DESC']);

        $order = $this->orders->matching($criteria)->first();

        if ($order instanceof Order) {
            return $order->getMileage();
        }

        return null;
    }

    public function getGosnomer(): ?string
    {
        if (null === $this->gosnomer) {
            return null;
        }

        $roman = ['A', 'B', 'E', 'K', 'M', 'H', 'O', 'P', 'C', 'T', 'Y', 'X'];
        $cyrillic = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'];

        return \str_replace($cyrillic, $roman, \mb_convert_case($this->gosnomer, MB_CASE_UPPER, 'UTF-8'));
    }

    public function setGosnomer(string $gosnomer = null): void
    {
        $this->gosnomer = $gosnomer;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return CarRecommendation[]
     */
    public function getRecommendations(Criteria $criteria = null): array
    {
        $criteria = $criteria ?: Criteria::create()->andWhere(Criteria::expr()->isNull('expiredAt'));

        return $this->recommendations->matching($criteria)->getValues();
    }

    public function addRecommendation(CarRecommendation $recommendation): void
    {
        $this->recommendations[] = $recommendation;
    }

    public function getCarModificationDisplayName(): string
    {
        return (string) $this->carModel;
    }
}
