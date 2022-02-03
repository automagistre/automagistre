<?php

declare(strict_types=1);

namespace App\Tenant\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Tenant
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public TenantId $id;

    /**
     * @ORM\Column(unique=true)
     */
    public string $identifier;

    /**
     * @ORM\Id
     * @ORM\Column
     */
    public GroupId $groupId;

    /**
     * @ORM\Column
     */
    public string $name;

    public function __construct(TenantId $id, string $identifier, GroupId $groupId, string $displayName)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->groupId = $groupId;
        $this->name = $displayName;
    }

    public function toId(): TenantId
    {
        return $this->id;
    }
}
