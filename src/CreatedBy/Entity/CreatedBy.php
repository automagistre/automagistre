<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\User\Entity\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Tenant\Entity\TenantEntity;

/**
 * @ORM\Entity(readOnly=true)
 */
class CreatedBy extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="user_id")
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
