<?php

declare(strict_types=1);

namespace App\EasyAdmin\Metrics;

use Artprima\PrometheusMetricsBundle\Metrics\MetricsCollectorInitTrait;
use Artprima\PrometheusMetricsBundle\Metrics\TerminateMetricsCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
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
            ['entity', 'action', 'user', 'method', 'code'],
        );

        $counter->inc([
            (string) $request->query->get('entity'),
            (string) $request->query->get('action'),
            $user->getUserIdentifier(),
            $request->getMethod(),
            (string) $response->getStatusCode(),
        ]);

        $this->collectExecutionTime($request);
    }

    private function collectExecutionTime(Request $request): void
    {
        $executionTime = $request->attributes->get('easyadmin_execution_time');

        if (null === $executionTime) {
            return;
        }

        $histogram = $this->collectionRegistry->getOrRegisterHistogram(
            $this->namespace,
            'easyadmin_execution_time',
            '',
            ['entity', 'action', 'method', 'tenant'],
            [0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0, 15.0, 20.0, 25.0, 60.0],
        );

        $histogram->observe(
            $executionTime,
            [
                (string) $request->query->get('entity'),
                (string) $request->query->get('action'),
                $request->getMethod(),
                (string) $request->attributes->get('tenant'),
            ],
        );
    }
}
