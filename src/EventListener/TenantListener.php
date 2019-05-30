<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\Tenant;
use App\Router\RoutePreGenerate;
use App\State;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantListener implements EventSubscriberInterface
{
    /**
     * @var State
     */
    private $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 31],
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
        // TODO Refactor this expression
        if (!\in_array($request->attributes->get('_route'), [
            'easyadmin',
            'admin_part_explorer',
            'admin_report_profit',
            'admin_report_part_sell',
        ], true)) {
            return;
        }

        $tenant = $this->validate($request->attributes->get('tenant'));
        if (null === $tenant) {
            throw new BadRequestHttpException('Tenant invalid or not exist.');
        }

        if (!Tenant::isValid($tenant)) {
            throw new NotFoundHttpException(\sprintf('Undefined tenant "%s"', $tenant));
        }

        $this->state->tenant(Tenant::fromIdentifier($tenant));
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
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
            throw new InvalidArgumentException(\sprintf('Undefined tenant "%s"', $identifier));
        }

        $this->state->tenant(Tenant::fromIdentifier($identifier));
    }

    public function onRouterPreGenerate(GenericEvent $event): void
    {
        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        // TODO Refactor this expression
        if ('easyadmin' !== $name && 0 !== \strpos($name, 'admin_')) {
            return;
        }

        if (\array_key_exists('tenant', $parameters)) {
            return;
        }

        $parameters['tenant'] = $this->state->tenant()->getIdentifier();

        $event->setArguments(['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType]);
    }

    /**
     * @param mixed $tenant
     */
    private function validate($tenant): ?string
    {
        return \is_string($tenant) ? $tenant : null;
    }
}
