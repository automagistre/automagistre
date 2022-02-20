<?php

declare(strict_types=1);

namespace App\EasyAdmin\Metrics;

use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInitTrait;
use Artprima\PrometheusMetricsBundle\Metrics\TerminateMetricsCollectorInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Security\Core\Security;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class EasyAdminMetricsCollector implements TerminateMetricsCollectorInterface
{
    use MetricsCollectorInitTrait;
    private const NAME = 'easyadmin';

    public function __construct(private Security $security)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function collectResponse(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $user = $this->security->getUser();

        if (null === $user || !$event->isMainRequest() || 'easyadmin' !== $request->attributes->get('_route')) {
            return;
        }

        $counter = $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'easyadmin_request_total',
            'easyadmin request total',
            ['entity', 'action', 'user', 'method', 'code', 'execution_time', 'tenant'],
        );

        $counter->inc([
            (string) $request->query->get('entity'),
            (string) $request->query->get('action'),
            $user->getUserIdentifier(),
            $request->getMethod(),
            (string) $response->getStatusCode(),
            (string) $request->attributes->get('easyadmin_execution_time'),
            (string) $request->attributes->get('tenant'),
        ]);
    }
}
