<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Landlord\CarModel;
use App\Enum\CarTransmission;
use App\Enum\CarWheelDrive;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Equipment
{
    use Identity;

    /**
     * @var CarModel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\CarModel")
     */
    public $model;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $engine;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $engineCapacity;

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

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4)
     */
    public $period;

    /**
     * @var Line[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\MC\Line", mappedBy="equipment")
     */
    public $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }
}
