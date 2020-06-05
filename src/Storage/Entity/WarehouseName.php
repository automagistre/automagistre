<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class WarehouseName
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="warehouse_id")
     */
    private WarehouseId $warehouseId;

    /**
     * @ORM\Column()
     */
    private string $name;

    public function __construct(WarehouseId $warehouseId, string $name)
    {
        $this->id = Uuid::uuid6();
        $this->warehouseId = $warehouseId;
        $this->name = $name;
    }
}
