<?php

declare(strict_types=1);

namespace App\Tenant\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class TenantGroupEntity
{
    /**
     * @psalm-readonly
     *
     * @ORM\Column(type="tenant_group_id")
     */
    public GroupId $tenantGroupId;
}
