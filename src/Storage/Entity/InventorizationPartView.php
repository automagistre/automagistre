<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationPartView extends TenantEntity
{
    /**
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
     * @ORM\Column(type="integer")
     */
    public int $inStock;

    /**
     * @ORM\Column(type="integer")
     */
    public int $reserved;
}
