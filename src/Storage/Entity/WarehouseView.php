<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use function implode;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="warehouse_view")
 *
 * @psalm-suppress MissingConstructor
 */
class WarehouseView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="warehouse_id")
     */
    public WarehouseId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="integer")
     */
    public int $depth;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseView::class, fetch="EAGER")
     */
    public ?WarehouseView $parent = null;

    public function toId(): WarehouseId
    {
        return $this->id;
    }

    public function __toString(): string
    {
        $name = [$this->name];

        $parent = $this->parent;
        while (null !== $parent) {
            $name = [$parent->name, ...$name];

            $parent = $parent->parent;
        }

        return implode(' / ', $name);
    }
}
