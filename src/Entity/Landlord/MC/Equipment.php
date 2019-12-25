<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Car\Entity\Equipment as CarEquipment;
use App\Car\Entity\Model;
use App\Doctrine\ORM\Mapping\Traits\Identity;
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
     * @ORM\ManyToOne(targetEntity="App\Car\Entity\Model")
     */
    public ?Model $model = null;

    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Car\Entity\Equipment")
     */
    public ?CarEquipment $equipment = null;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    public int $period = 0;

    /**
     * @var Collection<int, Line>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Landlord\MC\Line", mappedBy="equipment")
     */
    public ?Collection $lines = null;

    public function __construct()
    {
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }
}
