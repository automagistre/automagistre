<?php

declare(strict_types=1);

namespace App\MessageBus;

use App\Nsq\Envelope as NsqEnvelop;
use App\Nsq\Nsq;
use Generator;
use LogicException;
use Ramsey\Uuid\Uuid;
use Sentry\Util\JSON;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class NsqTransport implements TransportInterface
{
    private Nsq $nsq;

    private SerializerInterface $serializer;

    private string $topic;

    private ?Generator $subscriber = null;

    public function __construct(Nsq $nsq, SerializerInterface $serializer, string $topic)
    {
        $this->nsq = $nsq;
        $this->serializer = $serializer;
        $this->topic = $topic;
    }

    public function __destruct()
    {
        if (null !== $this->subscriber) {
            $this->subscriber->send(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $envelope): Envelope
    {
        $message = $this->getMessage($envelope);
        if (null !== $message) {
            $message->retry(($message->attempts <= 60 ? $message->attempts : 60) * 1000);

            return $envelope;
        }

        $trackingId = Uuid::uuid6()->toString();
        $body = [
            'payload' => $this->serializer->encode($envelope),
            'trackingId' => $trackingId,
        ];

        $this->nsq->pub($this->topic, JSON::encode($body));

        return $envelope->with(new TransportMessageIdStamp($trackingId));
    }

    /**
     * {@inheritdoc}
     */
    public function get(): iterable
    {
        $subscriber = $this->subscriber;
        if (null === $subscriber) {
            $this->subscriber = $subscriber = $this->nsq->subscribe($this->topic, 'tenant');
        } else {
            $subscriber->next();
        }

        /** @var NsqEnvelop|null $message */
        $message = $subscriber->current();

        if (null === $message) {
            return [];
        }

        [
            'payload' => $payload,
            'trackingId' => $trackingId,
        ] = JSON::decode($message->body);

        try {
            $envelope = $this->serializer->decode($payload);
        } catch (MessageDecodingFailedException $e) {
            // TODO ?

            throw $e;
        }

        return [
            $envelope->with(
                new NsqReceivedStamp($message),
                new TransportMessageIdStamp($trackingId),
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function ack(Envelope $envelope): void
    {
        $message = $this->getMessage($envelope);
        if (!$message instanceof NsqEnvelop) {
            throw new LogicException('Returned envelop doesn\'t related to NsqMessage.');
        }

        $message->ack();
    }

    /**
     * {@inheritdoc}
     */
    public function reject(Envelope $envelope): void
    {
        throw new LogicException('This shit want to reject my event.');
    }

    private function getMessage(Envelope $envelope): ?NsqEnvelop
    {
        $stamp = $envelope->last(NsqReceivedStamp::class);
        if (!$stamp instanceof NsqReceivedStamp) {
            return null;
        }

        return $stamp->envelope;
    }
}
