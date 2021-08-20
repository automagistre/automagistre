<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Tenant\Entity\TenantEntity;

/**
 * @ORM\Entity()
 *
 * @psalm-immutable
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="inventorization_id")
     */
    public InventorizationId $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public ?DateTimeImmutable $closedAt = null;
}
