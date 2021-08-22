<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Enum\Tenant;
use App\Tenant\State;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Throwable;
use function getenv;
use function is_string;
use function Sentry\captureException;

final class TenantListener implements EventSubscriberInterface
{
    public function __construct(private State $state, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 31],
            ConsoleEvents::COMMAND => ['onConsoleCommand', 2500],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $tenantName = match (true) {
            $request->attributes->has('tenant') => $request->attributes->get('tenant'),
            $request->headers->has('X-Tenant-Name') => $request->attributes->get('X-Tenant-Name'),
            default => null,
        };

        $tenant = null;

        try {
            if (is_string($tenantName)) {
                $tenant = Tenant::fromIdentifier($tenantName);
            }
        } catch (Throwable $e) {
            captureException($e);
        }

        $this->state->set($tenant);
    }

    public function onConsoleCommand(): void
    {
        $identifier = getenv('TENANT');

        if (!is_string($identifier)) {
            return;
        }

        $this->state->set(Tenant::fromIdentifier($identifier));
    }
}
