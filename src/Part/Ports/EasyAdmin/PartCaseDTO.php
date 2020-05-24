<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use App\Part\Domain\PartId;
use App\Vehicle\Domain\VehicleId;
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
