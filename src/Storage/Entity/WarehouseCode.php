<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class WarehouseCode extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="warehouse_id")
     */
    private WarehouseId $warehouseId;

    /**
     * @ORM\Column
     */
    private string $code;

    public function __construct(WarehouseId $warehouseId, string $code)
    {
        $this->id = Uuid::uuid6();
        $this->warehouseId = $warehouseId;
        $this->code = $code;
    }
}
