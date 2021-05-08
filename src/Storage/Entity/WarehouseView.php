<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="warehouse_id", nullable=true)
     */
    public ?WarehouseId $parentId = null;

    public function toId(): WarehouseId
    {
        return $this->id;
    }
}
