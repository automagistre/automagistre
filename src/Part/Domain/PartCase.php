<?php

declare(strict_types=1);

namespace App\Part\Domain;

use App\Vehicle\Domain\VehicleId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"part_id", "vehicle_id"})
 *     }
 * )
 */
class PartCase
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="vehicle_id")
     */
    private VehicleId $vehicleId;

    public function __construct(PartId $part, VehicleId $vehicleId)
    {
        $this->partId = $part;
        $this->vehicleId = $vehicleId;
    }
}
