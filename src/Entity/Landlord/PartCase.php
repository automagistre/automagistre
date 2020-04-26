<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Car\Entity\Model;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"part_id", "car_model_id"})
 *     }
 * )
 */
class PartCase
{
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity=Part::class)
     */
    public ?Part $part = null;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class)
     */
    public ?Model $carModel = null;

    public function __construct(Part $part = null, Model $carModel = null)
    {
        $this->part = $part;
        $this->carModel = $carModel;
    }
}
