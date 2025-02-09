<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class InventorizationPart extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public InventorizationId $inventorizationId;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(InventorizationId $inventorizationId, PartId $partId, int $quantity)
    {
        $this->inventorizationId = $inventorizationId;
        $this->partId = $partId;
        $this->quantity = $quantity;
    }
}
