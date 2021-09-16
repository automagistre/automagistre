<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @psalm-immutable
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
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
