<?php

declare(strict_types=1);

namespace App\MessageBus;

use App\Nsq\Config;
use App\Nsq\Consumer;
use App\Nsq\Envelope as NsqEnvelop;
use App\Nsq\Publisher;
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
    private Config $config;

    private SerializerInterface $serializer;

    private string $topic;

    private ?Consumer $consumer = null;

    private ?Publisher $publisher = null;

    private ?Generator $subscriber = null;

    public function __construct(Config $config, SerializerInterface $serializer, string $topic)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->topic = $topic;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $envelope): Envelope
    {
        $nsqEnvelop = $this->getNsqEnvelop($envelope);
        if (null !== $nsqEnvelop) {
            $nsqEnvelop->retry(
                ($nsqEnvelop->message->attempts <= 60 ? $nsqEnvelop->message->attempts : 60) * 1000
            );

            return $envelope;
        }

        $trackingId = Uuid::uuid6()->toString();
        $body = [
            'payload' => $this->serializer->encode($envelope),
            'trackingId' => $trackingId,
        ];

        $this->getPublisher()->pub($this->topic, JSON::encode($body));

        return $envelope->with(new TransportMessageIdStamp($trackingId));
    }

    /**
     * {@inheritdoc}
     */
    public function get(): iterable
    {
        $subscriber = $this->subscriber;
        if (null === $subscriber) {
            $this->subscriber = $subscriber = $this->getConsumer()->subscribe($this->topic, 'tenant');
        } else {
            $subscriber->next();
        }

        /** @var NsqEnvelop|null $nsqEnvelop */
        $nsqEnvelop = $subscriber->current();

        if (null === $nsqEnvelop) {
            return [];
        }

        [
            'payload' => $payload,
            'trackingId' => $trackingId,
        ] = JSON::decode($nsqEnvelop->message->body);

        try {
            $envelope = $this->serializer->decode($payload);
        } catch (MessageDecodingFailedException $e) {
            // TODO ?

            throw $e;
        }

        return [
            $envelope->with(
                new NsqReceivedStamp($nsqEnvelop),
                new TransportMessageIdStamp($trackingId),
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function ack(Envelope $envelope): void
    {
        $message = $this->getNsqEnvelop($envelope);
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

    private function getNsqEnvelop(Envelope $envelope): ?NsqEnvelop
    {
        $stamp = $envelope->last(NsqReceivedStamp::class);
        if (!$stamp instanceof NsqReceivedStamp) {
            return null;
        }

        return $stamp->envelope;
    }

    private function getConsumer(): Consumer
    {
        return $this->consumer ??= new Consumer($this->config);
    }

    private function getPublisher(): Publisher
    {
        return $this->publisher ??= new Publisher($this->config);
    }
}
