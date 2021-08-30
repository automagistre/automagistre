<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;

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
