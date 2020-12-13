<?php

declare(strict_types=1);

namespace App\Publish\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Publish
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $entityId;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $published;

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
