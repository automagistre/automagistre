<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\PartRelation;
use App\Part\Domain\Part;
use App\Storage\Enum\Source;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(columns={"part_id", "created_at"}),
 *     }
 * )
 */
class Motion
{
    use Identity;
    use CreatedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Embedded(class=PartRelation::class)
     */
    private PartRelation $part;

    /**
     * @ORM\Column(type="motion_source_enum")
     */
    private Source $source;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(
        Part $part,
        int $quantity,
        Source $source,
        UuidInterface $sourceId,
        string $description = null
    ) {
        $this->part = new PartRelation($part);
        $this->quantity = $quantity;
        $this->source = $source;
        $this->sourceId = $sourceId;
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
