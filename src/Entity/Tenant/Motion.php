<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\PartRelation;
use App\Entity\Landlord\Part;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="PART_IDX", columns={"part_id"})
 *     }
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({
 *     "0": "App\Entity\Tenant\MotionOld",
 *     "1": "App\Entity\Tenant\MotionOrder",
 *     "2": "App\Entity\Tenant\MotionIncome",
 *     "3": "App\Entity\Tenant\MotionManual",
 * })
 */
abstract class Motion
{
    use Identity;
    use CreatedAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var PartRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\PartRelation")
     */
    private $part;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    public function __construct(Part $part, int $quantity, string $description)
    {
        $this->part = new PartRelation($part);
        $this->quantity = $quantity;
        $this->description = $description;
    }

    public function getPart(): Part
    {
        return $this->part->entity();
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
