<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Vehicle\Entity\Embedded\Equipment as CarEquipment;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class McEquipment
{
    use Identity;

    /**
     * @ORM\Column(type="mc_equipment_id")
     */
    public McEquipmentId $uuid;

    /**
     * @ORM\Column(type="vehicle_id", nullable=true)
     */
    public ?VehicleId $vehicleId = null;

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
     * @var Collection<int, McLine>
     *
     * @ORM\OneToMany(targetEntity=McLine::class, mappedBy="equipment")
     */
    public ?Collection $lines = null;

    public function __construct()
    {
        $this->uuid = McEquipmentId::generate();
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }

    public function toId(): McEquipmentId
    {
        return $this->uuid;
    }
}
