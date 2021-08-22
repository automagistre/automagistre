<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Event\TenantChanged;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

final class RequestContextOnTenantChanged implements EventSubscriberInterface
{
    public function __construct(private RouterInterface $router)
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
        $this->router
            ->getContext()
            ->setParameter('tenant', $event->tenant?->toName())
        ;
    }
}
