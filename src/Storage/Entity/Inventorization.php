<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Inventorization extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     *
     * @psalm-readonly
     */
    public InventorizationId $id;

    /**
     * @ORM\OneToOne(targetEntity=InventorizationClose::class, mappedBy="inventorization", cascade={"persist"})
     */
    private ?InventorizationClose $close = null;

    public function __construct(InventorizationId $id = null)
    {
        $this->id = $id ?? InventorizationId::generate();
    }

    public function toId(): InventorizationId
    {
        return $this->id;
    }

    public function close(): void
    {
        if (null !== $this->close) {
            return;
        }

        $this->close = new InventorizationClose($this);
    }

    public function isClosed(): bool
    {
        return null !== $this->close;
    }
}
