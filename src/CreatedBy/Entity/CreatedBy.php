<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\Keycloak\Entity\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 */
class CreatedBy
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\Column
     */
    public UserId $userId;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    private function __construct(UuidInterface $id, UserId $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->createdAt = new DateTimeImmutable();
    }
}
