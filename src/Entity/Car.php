<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @var CarModification
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarModification")
     * @ORM\JoinColumn
     */
    private $carModification;

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
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $owner;

    /**
     * license plate.
     *
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $gosnomer;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="sprite_id", type="integer", nullable=true)
     */
    private $spriteId;

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
        $this->orders = new ArrayCollection();
        $this->recommendations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s, (%s)', $this->getCarModificationDisplayName(), $this->getGosnomer());
    }

    /**
     * @return CarModel
     */
    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    /**
     * @param CarModel $carModel
     */
    public function setCarModel(CarModel $carModel): void
    {
        $this->carModel = $carModel;
    }

    /**
     * @return CarModification
     */
    public function getCarModification(): ?CarModification
    {
        return $this->carModification;
    }

    /**
     * @param CarModification $carModification
     */
    public function setCarModification(CarModification $carModification): void
    {
        $this->carModification = $carModification;
    }

    /**
     * @return string
     */
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

    /**
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return Operand
     */
    public function getOwner(): ?Operand
    {
        return $this->owner;
    }

    /**
     * @param Operand $owner
     */
    public function setOwner(Operand $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getMileage(): ?int
    {
        /** @var Order $order */
        if ($order = $this->orders->last()) {
            return $order->getMileage();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getGosnomer(): ?string
    {
        if (!$this->gosnomer) {
            return null;
        }

        $roman = ['A', 'B', 'E', 'K', 'M', 'H', 'O', 'P', 'C', 'T', 'Y', 'X'];
        $cyrillic = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'];

        return str_replace($cyrillic, $roman, mb_convert_case($this->gosnomer, MB_CASE_UPPER, 'UTF-8'));
    }

    /**
     * @param string $gosnomer
     */
    public function setGosnomer(string $gosnomer = null): void
    {
        $this->gosnomer = $gosnomer;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @param Criteria|null $criteria
     *
     * @return CarRecommendation[]
     */
    public function getRecommendations(Criteria $criteria = null): array
    {
        $criteria = $criteria ?: Criteria::create()->andWhere(Criteria::expr()->isNull('expiredAt'));

        return $this->recommendations->matching($criteria)->toArray();
    }

    public function addRecommendation(CarRecommendation $recommendation): void
    {
        $this->recommendations[] = $recommendation;
    }

    public function getCarModificationDisplayName(): string
    {
        return (string) ($this->carModification ? $this->carModification->getDisplayName() : $this->carModel);
    }
}
