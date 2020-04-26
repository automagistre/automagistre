<?php

declare(strict_types=1);

namespace App\Part\Domain;

use App\Doctrine\ORM\Mapping\Traits\Identity;
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
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity="Part")
     */
    public Part $part;

    /**
     * @ORM\Column(type="tenant_enum")
     */
    public Tenant $tenant;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    public function __construct(Part $part, Tenant $tenant, int $quantity)
    {
        $this->part = $part;
        $this->tenant = $tenant;
        $this->quantity = $quantity;
    }
}
