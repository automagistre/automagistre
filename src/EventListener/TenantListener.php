<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\DBAL\SwitchableConnection;
use Doctrine\ORM\Events;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantListener implements EventSubscriberInterface
{
    /**
     * @var SwitchableConnection
     */
    private $connection;

    public function __construct(SwitchableConnection $connection)
    {
        $this->connection = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 31],
            ConsoleEvents::COMMAND => ['onConsoleCommand'],
            Events::postLoad => 'onDoctrinePostLoad',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        $host = $request->query->get('host');
        if (!\is_string($host)) {
            return;
        }

        $this->connection->switch($host);
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $input = $event->getInput();

        $command->getDefinition()->addOption(
            new InputOption('tenant', null, InputOption::VALUE_OPTIONAL, 'tenant', null)
        );

        $input->bind($command->getDefinition());

        $tenant = $input->getOption('tenant');
        if (!\is_string($tenant)) {
            return;
        }

        $this->connection->switch($tenant);
    }

    public function onDoctrinePostLoad(): void
    {
        dd(\func_get_args());
    }
}
