<?php

declare(strict_types=1);

namespace App\Publish\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class Publish extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public UuidInterface $entityId;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $published;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, UuidInterface $entityId, bool $published)
    {
        $this->id = $id;
        $this->entityId = $entityId;
        $this->published = $published;
    }

    public static function create(UuidInterface $entityId, bool $published): self
    {
        return new self(
            Uuid::uuid6(),
            $entityId,
            $published,
        );
    }
}
