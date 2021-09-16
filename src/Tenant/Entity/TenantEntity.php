<?php

declare(strict_types=1);

namespace App\Tenant\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class TenantEntity
{
    /**
     * @psalm-readonly
     *
     * @ORM\Column
     */
    public TenantId $tenantId;
}
