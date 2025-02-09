<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Warehouse extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public WarehouseId $id;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(WarehouseId $id)
    {
        $this->id = $id;
    }

    public function toId(): WarehouseId
    {
        return $this->id;
    }
}
