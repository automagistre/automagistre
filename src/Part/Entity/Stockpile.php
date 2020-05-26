<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Tenant\Tenant;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"part_id", "tenant"})
 *     },
 *     indexes={@ORM\Index(columns={"part_id", "tenant", "quantity"})}
 * )
 */
class Stockpile
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="tenant_enum")
     */
    public Tenant $tenant;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    public function __construct(PartId $partId, Tenant $tenant, int $quantity)
    {
        $this->partId = $partId;
        $this->tenant = $tenant;
        $this->quantity = $quantity;
    }
}
