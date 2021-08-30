<?php

declare(strict_types=1);

namespace App\Publish\Entity;

use App\CreatedBy\Entity\Blamable;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="publish_view")
 *
 * @psalm-suppress MissingConstructor
 */
class PublishView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $published;

    /**
     * @ORM\Embedded(class=Blamable::class)
     */
    public Blamable $created;
}
