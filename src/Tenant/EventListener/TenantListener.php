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
use function count;
use function explode;
use function getenv;
use function is_string;

final class TenantListener implements EventSubscriberInterface
{
    public function __construct(private State $state, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2500],
            ConsoleEvents::COMMAND => ['onConsoleCommand', 2500],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $pieces = explode('.', $request->getHost());

        $tenant = match (true) {
            count($pieces) >= 3 => Tenant::fromIdentifier($pieces[0]),
            default => null,
        };

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
