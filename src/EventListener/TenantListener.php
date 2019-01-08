<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\DBAL\SwitchableConnection;
use App\Entity\Landlord\Tenant;
use App\Router\ListeningRouterEvents;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantListener implements EventSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RegistryInterface $registry, RequestStack $requestStack)
    {
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 31],
            ConsoleEvents::COMMAND => ['onConsoleCommand'],
            ListeningRouterEvents::PRE_GENERATE => 'onRouterPreGenerate',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        if ('easyadmin' !== $request->attributes->get('_route')) {
            return;
        }

        $this->handleTenant($request->attributes->get('tenant'), false);
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $input = $event->getInput();

        $command->getDefinition()->addOption(
            new InputOption('tenant', null, InputOption::VALUE_OPTIONAL, 'tenant', null)
        );

        $input->bind($command->getDefinition());

        if (false === \strpos($command->getName(), 'doctrine')) {
            return;
        }

        if ($input->hasOption('db') && 'landlord' === $input->getOption('db')) {
            return;
        }

        if ($input->hasOption('em') && 'landlord' === $input->getOption('em')) {
            return;
        }

        $this->handleTenant($input->getOption('tenant'), true);
    }

    public function onRouterPreGenerate(GenericEvent $event): void
    {
        ['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType] = $event->getArguments();

        if ('easyadmin' !== $name) {
            return;
        }

        if (\array_key_exists('tenant', $parameters)) {
            return;
        }

        $parameters['tenant'] = $this->requestStack->getMasterRequest()->attributes->get('tenant');

        $event->setArguments(['name' => $name, 'parameters' => $parameters, 'referenceType' => $referenceType]);
    }

    /**
     * @param mixed $tenantName
     */
    private function handleTenant($tenantName, bool $cli): void
    {
        if (!\is_string($tenantName)) {
            $message = 'Tenant required';
            throw $cli
                ? new InvalidOptionException($message)
                : new NotFoundHttpException($message);
        }

        $tenant = $this->registry->getEntityManagerForClass(Tenant::class)
            ->getRepository(Tenant::class)
            ->findOneBy(['identifier' => $tenantName]);

        if (!$tenant instanceof Tenant) {
            throw new NotFoundHttpException();
        }

        $connection = $this->registry->getConnection('tenant');
        if (!$connection instanceof SwitchableConnection) {
            throw new LogicException('SwitchableConnection required');
        }

        $options = $tenant->database;
        $connection->switch($options->host, $options->name);
    }
}
