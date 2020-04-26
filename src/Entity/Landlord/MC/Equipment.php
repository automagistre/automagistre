<?php

declare(strict_types=1);

namespace App\Entity\Landlord\MC;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Vehicle\Domain\Equipment as CarEquipment;
use App\Vehicle\Domain\Model;
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
     * @ORM\ManyToOne(targetEntity=Model::class)
     */
    public ?Model $model = null;

    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class=CarEquipment::class)
     */
    public ?CarEquipment $equipment = null;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    public int $period = 0;

    /**
     * @var Collection<int, Line>
     *
     * @ORM\OneToMany(targetEntity=Line::class, mappedBy="equipment")
     */
    public ?Collection $lines = null;

    public function __construct()
    {
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }
}
