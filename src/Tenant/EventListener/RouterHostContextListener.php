<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Tenant;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Routing\RouterInterface;
use function sprintf;

final class RouterHostContextListener implements EventSubscriberInterface
{
    public function __construct(private RouterInterface $router, private Tenant $tenant)
    {
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
            ->setScheme('https')
        ;
    }
}
