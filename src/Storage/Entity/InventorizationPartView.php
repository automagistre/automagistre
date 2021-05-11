<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationPartView
{
    /**
     * @ORM\Column(type="inventorization_id")
     */
    public InventorizationId $inventorizationId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
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
