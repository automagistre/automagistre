<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\CreatedBy\Attributes as CreatedBy;
use App\Part\Entity\PartId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function abs;
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

    public function __construct(PartId $id)
    {
        $this->id = $id;
        $this->motions = new ArrayCollection();
    }

    public function toId(): PartId
    {
        return $this->id;
    }

    public function increase(int $quantity, MotionSource $source, string $description = null): void
    {
        $this->motions[] = new Motion(
            $this,
            $quantity,
            $source,
            $description,
        );
    }

    public function decrease(int $quantity, MotionSource $source, string $description = null): void
    {
        $this->motions[] = new Motion(
            $this,
            0 - $quantity,
            $source,
            $description,
        );
    }

    public function actualize(int $quantity, MotionSource $source, string $description = null): void
    {
        $inStock = array_reduce($this->motions->toArray(), static function (int $quantity, Motion $motion): int {
            return $quantity + $motion->getQuantity();
        }, 0);

        $delta = $quantity - $inStock;

        if (0 === $delta) {
            return;
        }

        if ($delta > 0) {
            $this->increase($delta, $source, $description);
        } else {
            $this->decrease(abs($delta), $source, $description);
        }
    }
}
