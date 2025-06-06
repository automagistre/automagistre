<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Tenant\Entity\TenantEntity;
use App\Vehicle\Entity\Embedded\Equipment as CarEquipment;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class McEquipment extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public McEquipmentId $id;

    /**
     * @ORM\Column
     */
    public VehicleId $vehicleId;

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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

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
