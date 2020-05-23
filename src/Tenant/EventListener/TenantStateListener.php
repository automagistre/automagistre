<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Shared\Router\RoutePreGenerate;
use App\State;
use App\Tenant\Tenant;
use function array_key_exists;
use function in_array;
use function is_string;
use function sprintf;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantStateListener implements EventSubscriberInterface
{
    private State $state;

    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(State $state, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->state = $state;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            ConsoleEvents::COMMAND => ['onConsoleCommand'],
            RoutePreGenerate::class => 'onRouterPreGenerate',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->isSupportedRoute($request->attributes->get('_route'))) {
            return;
        }

        $identifier = $this->validate($request->attributes->get('tenant'));
        if (null === $identifier) {
            throw new BadRequestHttpException('Tenant invalid or not exist.');
        }

        if (!Tenant::isValid($identifier)) {
            throw new NotFoundHttpException(sprintf('Undefined tenant "%s"', $identifier));
        }

        $tenant = Tenant::fromIdentifier($identifier);

        $this->state->tenant($tenant);

        if (!$this->authorizationChecker->isGranted('ACCESS', $tenant)) {
            throw new AccessDeniedHttpException(
                sprintf('You are not permitted to access "%s" tenant', $tenant->toName())
            );
        }
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        if (null === $command) {
            return;
        }

        $input = $event->getInput();

        $command->getDefinition()->addOption(
            new InputOption('tenant', null, InputOption::VALUE_OPTIONAL, 'tenant', null)
        );

        $input->bind($command->getDefinition());

        foreach (['db', 'em', 'connection'] as $option) {
            if ($input->hasOption($option) && 'landlord' === $input->getOption($option)) {
                return;
            }
        }

        $identifier = $this->validate($input->getOption('tenant'));
        if (null === $identifier) {
            return;
        }

        if (!Tenant::isValid($identifier)) {
            throw new InvalidArgumentException(sprintf('Undefined tenant "%s"', $identifier));
        }

        $this->state->tenant(Tenant::fromIdentifier($identifier));
    }

    public function onRouterPreGenerate(GenericEvent $event): void
    {
        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        if (!$this->isSupportedRoute($name)) {
            return;
        }

        if (!$this->state->isTenantDefined()) {
            return;
        }

        if (array_key_exists('tenant', $parameters)) {
            return;
        }

        $parameters['tenant'] = $this->state->tenant()->toIdentifier();

        $event->setArguments(['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType]);
    }

    /**
     * TODO Refactor this expression.
     */
    private function isSupportedRoute(string $routeName): bool
    {
        return in_array($routeName, [
            'easyadmin',
            'part_explorer',
            'report_profit',
            'report_part_sell',
        ], true);
    }

    /**
     * @param mixed $tenant
     */
    private function validate($tenant): ?string
    {
        return is_string($tenant) ? $tenant : null;
    }
}
