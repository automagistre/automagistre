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
class WarehouseCode extends TenantEntity
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
     * @ORM\Column
     */
    private string $code;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(WarehouseId $warehouseId, string $code)
    {
        $this->id = Uuid::uuid6();
        $this->warehouseId = $warehouseId;
        $this->code = $code;
    }
}
