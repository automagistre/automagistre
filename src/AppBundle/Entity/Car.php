<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Car
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
     * @var CarManufacturer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarManufacturer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $carManufacturer;

    /**
     * @var CarModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarModel")
     * @ORM\JoinColumn(nullable=true)
     */
    private $carModel;

    /**
     * @var CarModification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarModification")
     * @ORM\JoinColumn(nullable=true)
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
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn()
     */
    private $client;

    /**
     * @var Mileage[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mileage", mappedBy="car")
     */
    private $mileage;

    /**
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->mileage = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return CarManufacturer
     */
    public function getCarManufacturer(): ?CarManufacturer
    {
        return $this->carManufacturer;
    }

    /**
     * @param CarManufacturer $carManufacturer
     */
    public function setCarManufacturer(CarManufacturer $carManufacturer)
    {
        $this->carManufacturer = $carManufacturer;
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
    public function setCarModel(CarModel $carModel)
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
    public function setCarModification(CarModification $carModification)
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
    public function setVin(string $vin)
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
    public function setYear(int $year)
    {
        $this->year = $year;
    }

    /**
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Mileage
     */
    public function getMileage(): ?Mileage
    {
        return $this->mileage->last() ?: null;
    }

    /**
     * @param Mileage $mileage
     */
    public function setMileage(Mileage $mileage)
    {
        $this->mileage[] = $mileage;
    }

    /**
     * @return string
     */
    public function getGosnomer(): ?string
    {
        return $this->gosnomer;
    }

    /**
     * @param string $gosnomer
     */
    public function setGosnomer(string $gosnomer)
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
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getDisplayName(): string
    {
        return sprintf(
            '%s %s %s (%s)',
            $this->carManufacturer->getName(),
            $this->carModel->getName(),
            $this->carModification ? $this->carModification->getName() : '',
            $this->getGosnomer()
        );
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
