<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\PartRelation;
use App\Part\Domain\Part;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(columns={"part_id", "created_at"}),
 *     }
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "0": "App\Storage\Entity\MotionOld",
 *     "1": "App\Storage\Entity\MotionOrder",
 *     "2": "App\Storage\Entity\MotionIncome",
 *     "3": "App\Storage\Entity\MotionManual",
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
     * @ORM\Embedded(class=PartRelation::class)
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
