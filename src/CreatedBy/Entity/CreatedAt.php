<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class CreatedAt
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private DateTimeImmutable $createdAt;

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
    }
}
