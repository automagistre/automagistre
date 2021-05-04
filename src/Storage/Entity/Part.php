<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\CreatedBy\Attributes as CreatedBy;
use App\Part\Entity\PartId;
use App\Storage\Enum\Source;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use function array_reduce;

/**
 * @ORM\Entity()
 * @ORM\Table(name="storage_part")
 */
class Part
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
     */
    #[CreatedBy\Exclude]
    private PartId $id;

    /**
     * @var Collection<int, Motion>
     *
     * @ORM\OneToMany(targetEntity=Motion::class, mappedBy="part", cascade={"persist"})
     */
    private Collection $motions;

    /**
     * @var Collection<int, Inventorization>
     *
     * @ORM\OneToMany(targetEntity=Inventorization::class, mappedBy="part", cascade={"persist"})
     */
    private Collection $inventorizations;

    public function __construct(PartId $id)
    {
        $this->id = $id;
        $this->motions = new ArrayCollection();
        $this->inventorizations = new ArrayCollection();
    }

    public function toId(): PartId
    {
        return $this->id;
    }

    public function move(int $quantity, Source $source, UuidInterface $sourceId, string $description = null): void
    {
        $this->motions[] = new Motion(
            $this,
            $quantity,
            $source,
            $sourceId,
            $description,
        );
    }

    public function inventory(int $quantity, string $description = null): void
    {
        $inventorizationId = InventorizationId::generate();

        $this->inventorizations[] = new Inventorization(
            $inventorizationId,
            $this,
            $quantity,
            $description,
        );

        $inStock = array_reduce($this->motions->toArray(), static function (int $quantity, Motion $motion): int {
            return $quantity + $motion->getQuantity();
        }, 0);

        $delta = $quantity - $inStock;

        $this->move(
            $delta,
            Source::inventory(),
            $inventorizationId->toUuid(),
            $description,
        );
    }
}
