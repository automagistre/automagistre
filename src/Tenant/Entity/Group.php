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
     * @ORM\Column
     */
    public GroupId $id;

    /**
     * @ORM\Column
     */
    public string $identifier;

    /**
     * @ORM\Column(type="text")
     */
    public string $name;

    public function __construct(GroupId $id, string $identifier, string $name)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
    }
}
