<?php

declare(strict_types=1);

namespace App\MessageBus;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class EntityEventsListener implements EventSubscriberInterface
{
    private EntityRecordedMessageCollectorListener $collector;

    private MessageBusInterface $messageBus;

    public function __construct(EntityRecordedMessageCollectorListener $collector, MessageBusInterface $messageBus)
    {
        $this->collector = $collector;
        $this->messageBus = $messageBus;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onKernelTerminate(): void
    {
        foreach ($this->collector->eraseMessages() as $message) {
            $this->messageBus->dispatch($message);
        }
    }
}
