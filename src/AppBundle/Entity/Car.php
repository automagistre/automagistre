<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="car", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_3a7293440d99b39c56ff99074677931de71144cb", columns={"gosnomer"}), @ORM\UniqueConstraint(name="UQ_VIN", columns={"vin"})}, indexes={@ORM\Index(name="EID_IDX", columns={"eid"}), @ORM\Index(name="IDX_CAR_CLIENT", columns={"client_id"}), @ORM\Index(name="IDX_GOSNOMER", columns={"gosnomer"}), @ORM\Index(name="sprite_id", columns={"sprite_id"})})
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
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item")
     * @ORM\JoinColumn()
     */
    private $item;

    /**
     * @var Carmake
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmake")
     * @ORM\JoinColumn()
     */
    private $carmake;

    /**
     * @var Carmodel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmodel")
     * @ORM\JoinColumn()
     */
    private $carmodel;

    /**
     * @var Carmodification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmodification")
     * @ORM\JoinColumn()
     */
    private $carmodification;

    /**
     * @var string
     *
     * @ORM\Column(name="vin", type="string", length=17, nullable=true)
     */
    private $vin;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
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
     * @ORM\Column(name="gosnomer", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="eid", type="integer", nullable=true)
     */
    private $eid;

    /**
     * @var int
     *
     * @ORM\Column(name="sprite_id", type="integer", nullable=true)
     */
    private $spriteId;

    public function __construct()
    {
        $this->mileage = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Carmake
     */
    public function getCarmake(): ?Carmake
    {
        return $this->carmake;
    }

    /**
     * @param Carmake $carmake
     */
    public function setCarmake(Carmake $carmake)
    {
        $this->carmake = $carmake;
    }

    /**
     * @return Carmodel
     */
    public function getCarmodel(): ?Carmodel
    {
        return $this->carmodel;
    }

    /**
     * @param Carmodel $carmodel
     */
    public function setCarmodel(Carmodel $carmodel)
    {
        $this->carmodel = $carmodel;
    }

    /**
     * @return Carmodification
     */
    public function getCarmodification(): ?Carmodification
    {
        return $this->carmodification;
    }

    /**
     * @param Carmodification $carmodification
     */
    public function setCarmodification(Carmodification $carmodification)
    {
        $this->carmodification = $carmodification;
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

    public function getDisplayName(): string
    {
        return sprintf(
            '%s %s %s (%s)',
            $this->carmake->getName(),
            $this->carmodel->getName(),
            $this->carmodification ? $this->carmodification->getName() : '',
            $this->getGosnomer()
        );
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
