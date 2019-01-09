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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

        $tenant = $this->validate($request->attributes->get('tenant'));
        if (null === $tenant) {
            throw new BadRequestHttpException('Tenant invalid or not exist.');
        }

        $entity = $this->registry->getEntityManagerForClass(Tenant::class)
            ->getRepository(Tenant::class)
            ->findOneBy(['identifier' => $tenant]);

        if (!$entity instanceof Tenant) {
            throw new NotFoundHttpException(\sprintf('Tenant "%s" not exist.', $tenant));
        }

        $request->attributes->set('_tenant', $entity);
        $this->switch($tenant);
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

        foreach (['db', 'em', 'connection'] as $option) {
            if ($input->hasOption($option) && 'landlord' === $input->getOption($option)) {
                return;
            }
        }

        $tenant = $this->validate($input->getOption('tenant'));
        if (null === $tenant) {
            throw new InvalidOptionException('Tenant invalid or not exist.');
        }

        $this->switch($tenant);
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
     * @param mixed $tenant
     */
    private function validate($tenant): ?string
    {
        return \is_string($tenant) ? $tenant : null;
    }

    private function switch(string $tenant): void
    {
        $connection = $this->registry->getConnection('tenant');
        if (!$connection instanceof SwitchableConnection) {
            throw new LogicException('SwitchableConnection required');
        }

        $connection->switch($tenant);
    }
}
