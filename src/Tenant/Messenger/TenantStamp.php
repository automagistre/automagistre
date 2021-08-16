<?php

declare(strict_types=1);

namespace App\Tenant\Messenger;

use App\Tenant\Enum\Tenant;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class TenantStamp implements StampInterface
{
    public function __construct(private Tenant $tenant)
    {
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }
}
