<?php

declare(strict_types=1);

namespace App\MessageBus;

use Sentry\State\Scope;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use function get_object_vars;
use function json_encode;
use function Sentry\configureScope;
use const JSON_THROW_ON_ERROR;

final class SentryListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => ['onMessage'],
        ];
    }

    public function onMessage(WorkerMessageReceivedEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        configureScope(function (Scope $scope) use ($message): void {
            $scope->setContext('Messenger', [
                'event' => $message::class,
                'values' => json_encode(get_object_vars($message), JSON_THROW_ON_ERROR),
            ]);
        });
    }
}
