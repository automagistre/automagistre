<?php

declare(strict_types=1);

namespace App\Tenant\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tenant_group")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Column(type="tenant_group_id")
     */
    public GroupId $id;

    /**
     * @ORM\Column
     */
    public string $identifier;

    public function __construct(GroupId $id, string $identifier)
    {
        $this->id = $id;
        $this->identifier = $identifier;
    }
}
