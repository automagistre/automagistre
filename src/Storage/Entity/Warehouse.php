<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Warehouse
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="warehouse_id")
     */
    public WarehouseId $id;

    public function __construct(WarehouseId $id)
    {
        $this->id = $id;
    }
}
