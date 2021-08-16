<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Tenant\Enum\Tenant;
use Symfony\Contracts\Service\ResetInterface;
use LogicException;

final class State implements ResetInterface
{
    public ?Tenant $tenant = null;

    public function __construct()
    {
        $this->tenant = Tenant::fromEnv();
    }

    public function get(): Tenant
    {
        return $this->tenant ?? throw new LogicException('Tenant not defined.');
    }

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->tenant = null;
    }
}
