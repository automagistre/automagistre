<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\User\Entity\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class CreatedBy
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="user_id", nullable=true)
     */
    private ?UserId $userId;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private DateTimeImmutable $createdAt;

    private function __construct(UuidInterface $id, ?UserId $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->createdAt = new DateTimeImmutable();
    }
}
