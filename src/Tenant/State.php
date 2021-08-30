<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Tenant\Enum\Tenant;
use App\Tenant\Event\TenantChanged;
use LogicException;
use Sentry\State\Scope;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ResetInterface;
use function Sentry\configureScope;

final class State implements ResetInterface
{
    public ?Tenant $tenant = null;

    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function get(): Tenant
    {
        return $this->tenant ?? throw new LogicException('Tenant not defined.');
    }

    public function set(?Tenant $tenant): void
    {
        if ($this->tenant === $tenant) {
            return;
        }

        configureScope(static function (Scope $scope) use ($tenant): void {
            $scope->setTag('tenant', $tenant?->toIdentifier() ?? 'null');
        });

        $this->tenant = $tenant;

        $this->dispatcher->dispatch(new TenantChanged($tenant));
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->set(null);
    }
}
