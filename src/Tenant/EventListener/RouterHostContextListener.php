<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Tenant;
use function sprintf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Routing\RouterInterface;

final class RouterHostContextListener implements EventSubscriberInterface
{
    private RouterInterface $router;

    private Tenant $tenant;

    public function __construct(RouterInterface $router, Tenant $tenant)
    {
        $this->router = $router;
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
        ];
    }

    public function onWorkerStarted(): void
    {
        $this->router->getContext()
            ->setHost(sprintf('%s.automagistre.ru', $this->tenant->toIdentifier()))
            ->setScheme('https');
    }
}
