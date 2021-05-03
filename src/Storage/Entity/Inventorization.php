<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Inventorization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="inventorization_id")
     */
    private InventorizationId $id;

    /**
     * @ORM\ManyToOne(targetEntity=Part::class, inversedBy="inventorizations")
     */
    private Part $part;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(InventorizationId $id, Part $part, int $quantity, string $description = null)
    {
        $this->id = $id;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->description = $description;
    }
}
