<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Vehicle\Entity\VehicleId;
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
     * @ORM\Id
     * @ORM\Column
     */
    private PartCaseId $id;

    /**
     * @ORM\Column
     */
    private PartId $partId;

    /**
     * @ORM\Column
     */
    private VehicleId $vehicleId;

    public function __construct(PartId $part, VehicleId $vehicleId)
    {
        $this->id = PartCaseId::generate();
        $this->partId = $part;
        $this->vehicleId = $vehicleId;
    }
}
