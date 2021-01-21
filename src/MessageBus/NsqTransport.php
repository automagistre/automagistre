<?php

declare(strict_types=1);

namespace App\MessageBus;

use App\Nsq\Config;
use App\Nsq\Envelope as NsqEnvelop;
use App\Nsq\Subscriber;
use App\Nsq\Writer;
use Generator;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use LogicException;
use Ramsey\Uuid\Uuid;
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

    private Subscriber $subscriber;

    private ?Writer $publisher = null;

    private ?Generator $generator = null;

    public function __construct(Config $config, SerializerInterface $serializer, string $topic)
    {
        $this->config = $config;
        $this->subscriber = new Subscriber($this->config);
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

        $this->getPublisher()->pub($this->topic, json_encode($body, JSON_THROW_ON_ERROR));

        return $envelope->with(new TransportMessageIdStamp($trackingId));
    }

    /**
     * {@inheritdoc}
     */
    public function get(): iterable
    {
        $generator = $this->generator;
        if (null === $generator) {
            $this->generator = $generator = $this->subscriber->subscribe($this->topic, 'tenant');
        } else {
            $generator->next();
        }

        /** @var NsqEnvelop|null $nsqEnvelop */
        $nsqEnvelop = $generator->current();

        if (null === $nsqEnvelop) {
            return [];
        }

        [
            'payload' => $payload,
            'trackingId' => $trackingId,
        ] = json_decode($nsqEnvelop->message->body, true, 512, JSON_THROW_ON_ERROR);

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

    private function getPublisher(): Writer
    {
        return $this->publisher ??= new Writer($this->config);
    }
}
