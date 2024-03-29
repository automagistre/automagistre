<?php

declare(strict_types=1);

namespace App\Tenant\Event;

use App\Tenant\Entity\Tenant;

/**
 * @psalm-immutable
 */
final class TenantChanged
{
    public function __construct(public ?Tenant $tenant)
    {
    }
}
