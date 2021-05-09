<?php

declare(strict_types=1);

namespace App\MessageBus;

use Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class EntityEventsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityRecordedMessageCollectorListener $messageRecorder,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } catch (Exception $exception) {
            $this->messageRecorder->eraseMessages();

            throw $exception;
        }

        foreach ($this->messageRecorder->eraseMessages() as $recordedMessage) {
            $this->messageBus->dispatch($recordedMessage);
        }

        return $envelope;
    }
}
