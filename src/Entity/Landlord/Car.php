<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Uuid;
use App\Enum\Carcase;
use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use App\Enum\EngineType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"vin"})
 */
class Car
{
    use Identity;
    use Uuid;
    use CreatedAt;

    /**
     * @var CarModel
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\CarModel")
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
     * @Assert\Length(max="17")
     *
     * @ORM\Column(length=17, nullable=true, unique=true)
     */
    private $vin;

    /**
     * @var int|null
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Operand")
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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $mileage = 0;

    /**
     * @var CarRecommendation[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\CarRecommendation", mappedBy="car", cascade={"persist"})
     */
    private $recommendations;

    public function __construct()
    {
        $this->generateUuid();
        $this->engineType = EngineType::unknown();
        $this->wheelDrive = CarWheelDrive::unknown();
        $this->transmission = CarTransmission::unknown();
        $this->caseType = Carcase::unknown();
        $this->recommendations = new ArrayCollection();
    }

    public function __toString(): string
    {
        $string = $this->carModel->getDisplayName(false);

        $string .= null !== $this->year ? \sprintf(' - %sг.', $this->year) : '';

        return $string;
    }

    public function getRecommendationPrice(): array
    {
        $services = new Money(0, new Currency('RUB'));
        $parts = new Money(0, new Currency('RUB'));

        foreach ($this->getRecommendations() as $item) {
            $services = $services->add($item->getPrice());
            $parts = $parts->add($item->getTotalPartPrice());
        }

        return [$services, $parts, $services->add($parts)];
    }

    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    public function setCarModel(?CarModel $carModel): void
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

    public function setYear(?int $year): void
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

    public function setMileage(?int $mileage): void
    {
        if (null !== $mileage) {
            $this->mileage = $mileage;
        }
    }

    public function getMileage(): int
    {
        return $this->mileage;
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
