<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class WarehouseParent extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private WarehouseId $warehouseId;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?WarehouseId $warehouseParentId;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(WarehouseId $warehouseId, ?WarehouseId $warehouseParentId)
    {
        $this->id = Uuid::uuid6();
        $this->warehouseId = $warehouseId;
        $this->warehouseParentId = $warehouseParentId;
    }
}
