<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Doctrine\Registry;
use App\Tenant\Event\TenantChanged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TenantFilterParameterOnTenantChanged implements EventSubscriberInterface
{
    public function __construct(private Registry $registry)
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
        $this->registry->manager()
            ->getFilters()
            ->getFilter('tenant')
            ->setParameter('tenant_id', $event->tenant?->id)
        ;

        $this->registry->manager()
            ->getFilters()
            ->getFilter('tenant_group')
            ->setParameter('tenant_group_id', $event->tenant?->groupId)
        ;
    }
}
