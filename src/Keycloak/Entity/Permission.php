<?php

declare(strict_types=1);

namespace App\Keycloak\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_permission")
 */
class Permission extends TenantEntity
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

    public function __construct(UserId $userId)
    {
        $this->id = Uuid::uuid6();
        $this->userId = $userId;
    }
}
