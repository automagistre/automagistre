<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Doctrine\ConnectionSwitcher;
use App\Tenant\Event\TenantChanged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SwitchConnectionOnTenantChanged implements EventSubscriberInterface
{
    public function __construct(private ConnectionSwitcher $switcher)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TenantChanged::class => 'onTenantChanged',
        ];
    }

    public function onTenantChanged(TenantChanged $event): void
    {
        $this->switcher->switch($event->tenant);
    }
}
