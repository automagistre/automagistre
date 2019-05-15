<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\CarEquipment;
use App\Entity\Landlord\CarModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var CarEquipment
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\CarEquipment")
     */
    public $equipment;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4)
     */
    public $period;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\MC\Line", mappedBy="equipment")
     */
    public $lines;

    public function __construct()
    {
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }
}
