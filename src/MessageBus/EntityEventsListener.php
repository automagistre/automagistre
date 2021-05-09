<?php

declare(strict_types=1);

namespace App\MessageBus;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class EntityEventsListener implements EventSubscriberInterface
{
    public function __construct(
        private EntityRecordedMessageCollectorListener $collector,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::FINISH_REQUEST => 'onKernelTerminate',
            ConsoleEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onKernelTerminate(): void
    {
        foreach ($this->collector->eraseMessages() as $message) {
            $this->messageBus->dispatch($message);
        }
    }
}
