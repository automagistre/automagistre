<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;

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

    public function __construct(InventorizationId $inventorizationId, PartId $partId, int $quantity)
    {
        $this->inventorizationId = $inventorizationId;
        $this->partId = $partId;
        $this->quantity = $quantity;
    }
}
