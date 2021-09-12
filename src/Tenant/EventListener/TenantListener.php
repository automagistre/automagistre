<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Doctrine\Registry;
use App\Tenant\Entity\Tenant;
use App\Tenant\State;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use function getenv;
use function is_string;

final class TenantListener implements EventSubscriberInterface
{
    public function __construct(
        private Registry $registry,
        private State $state,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
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
            $request->headers->has('X-Tenant-Id') => $request->attributes->get('X-Tenant-Id'),
            default => null,
        };

        $tenant = $this->registry->findOneBy(Tenant::class, ['identifier' => $tenantName]);

        $this->state->set($tenant);
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if (null === $command) {
            return;
        }

        $command->getApplication()?->getDefinition()
            ->addOption(new InputOption('tenant', null, InputOption::VALUE_OPTIONAL))
        ;

        $command->mergeApplicationDefinition();

        $input = new ArgvInput();

        $input->bind($command->getDefinition());

        $identifier = $input->getOption('tenant');

        if (!is_string($identifier)) {
            $identifier = getenv('TENANT');
        }

        if (!is_string($identifier)) {
            return;
        }

        $this->state->set($this->registry->findOneBy(Tenant::class, ['identifier' => $identifier]));
    }
}
