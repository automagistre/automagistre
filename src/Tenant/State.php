<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Tenant\Enum\Tenant;
use Sentry\State\Scope;
use Symfony\Contracts\Service\ResetInterface;
use LogicException;
use function Sentry\configureScope;

final class State implements ResetInterface
{
    public ?Tenant $tenant = null;

    public function __construct()
    {
        $this->set(Tenant::fromEnv());
    }

    public function get(): Tenant
    {
        return $this->tenant ?? throw new LogicException('Tenant not defined.');
    }

    public function set(?Tenant $tenant): void
    {
        configureScope(static function (Scope $scope) use ($tenant): void {
            $scope->setTag('tenant', $tenant?->toIdentifier() ?? 'null');
        });

        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->set(null);
    }
}
