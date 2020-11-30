<?php

declare(strict_types=1);

namespace App\MC\Entity;

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
    /**
     * @ORM\Id
     * @ORM\Column(type="mc_equipment_id")
     */
    public McEquipmentId $id;

    /**
     * @ORM\Column(type="vehicle_id", nullable=true)
     */
    public ?VehicleId $vehicleId = null;

    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class=CarEquipment::class)
     */
    public CarEquipment $equipment;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    public int $period = 0;

    /**
     * @var Collection<int, McLine>
     *
     * @ORM\OneToMany(targetEntity=McLine::class, mappedBy="equipment")
     * @ORM\OrderBy(value={"position": "ASC"})
     */
    public ?Collection $lines = null;

    public function __construct(McEquipmentId $id)
    {
        $this->id = $id;
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }

    public function toId(): McEquipmentId
    {
        return $this->id;
    }
}
