<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Tenant\Entity\TenantEntity;

/**
 * @ORM\Entity
 */
class Warehouse extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="warehouse_id")
     */
    public WarehouseId $id;

    public function __construct(WarehouseId $id)
    {
        $this->id = $id;
    }

    public function toId(): WarehouseId
    {
        return $this->id;
    }
}
