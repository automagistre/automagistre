<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="UNIQUE_IDX", columns={"part_id", "car_model_id"})
 *     }
 * )
 */
class PartCase
{
    use Identity;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Part")
     */
    public $part;

    /**
     * @var CarModel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\CarModel")
     */
    public $carModel;

    public function __construct(Part $part = null, CarModel $carModel = null)
    {
        $this->part = $part;
        $this->carModel = $carModel;
    }
}
