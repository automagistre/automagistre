<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\CarEquipment;
use App\Enum\Carcase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
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
    use CreatedAt;

    /**
     * @var CarEquipment
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\CarEquipment")
     */
    public $equipment;

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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\CarRecommendation", mappedBy="car", cascade={"persist"})
     */
    private $recommendations;

    public function __construct()
    {
        $this->equipment = new CarEquipment();
        $this->caseType = Carcase::unknown();
        $this->recommendations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(bool $full = false): string
    {
        $string = $this->carModel->getDisplayName(false);

        if (null !== $this->year) {
            $string .= \sprintf(' - %sг.', $this->year);
        }

        if ($full) {
            $string .= \sprintf(' (%s)', $this->equipment->toString());
        }

        return $string;
    }

    public function getRecommendationPrice(string $type): Money
    {
        $price = null;

        foreach ($this->getRecommendations() as $item) {
            if ('service' === $type) {
                $item = $item->getPrice();
            } elseif ('part' === $type) {
                $item = $item->getTotalPartPrice();
            } else {
                throw new LogicException(\sprintf('Unexpected type "%s"', $type));
            }

            $price = $price instanceof Money ? $price->add($item) : $item;
        }

        if (null === $price) {
            throw new LogicException('Car not have actual recommendations');
        }

        return $price;
    }

    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    public function setCarModel(?CarModel $carModel): void
    {
        $this->carModel = $carModel;
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
        if (!$this->recommendations instanceof Selectable) {
            throw new LogicException(\sprintf('Collection must implement "%s"', Selectable::class));
        }

        $criteria = $criteria ?: Criteria::create()->andWhere(Criteria::expr()->isNull('expiredAt'));

        return $this->recommendations->matching($criteria)->getValues();
    }

    public function addRecommendation(CarRecommendation $recommendation): void
    {
        $this->recommendations[] = $recommendation;
    }

    public function getCarModificationDisplayName(): string
    {
        return $this->carModel->getDisplayName();
    }
}
