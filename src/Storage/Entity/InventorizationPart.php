<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class InventorizationPart
{
    /**
     * @ORM\Id()
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

    public function __construct(InventorizationId $inventorizationId, PartId $partId, int $quantity)
    {
        $this->inventorizationId = $inventorizationId;
        $this->partId = $partId;
        $this->quantity = $quantity;
    }
}
