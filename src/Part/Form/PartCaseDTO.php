<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Part\Entity\PartId;
use App\Vehicle\Entity\VehicleId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
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
}
