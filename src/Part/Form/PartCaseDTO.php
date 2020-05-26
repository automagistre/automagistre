<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use App\Vehicle\Entity\VehicleId;
use Symfony\Component\Validator\Constraints as Assert;

final class PartCaseDTO
{
    /**
     * @var PartId
     *
     * @Assert\NotBlank
     */
    public $partId;

    /**
     * @var VehicleId
     *
     * @Assert\NotBlank
     */
    public $vehicleId;

    public function __construct(PartId $partId, VehicleId $vehicleId)
    {
        $this->partId = $partId;
        $this->vehicleId = $vehicleId;
    }
}
