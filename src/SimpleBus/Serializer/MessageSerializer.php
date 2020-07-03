<?php

declare(strict_types=1);

namespace App\SimpleBus\Serializer;

use function class_exists;
use function get_class;
use function is_object;
use const JSON_UNESCAPED_SLASHES;
use LogicException;
use Sentry\Util\JSON;
use function sprintf;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MessageSerializer
{
    private NormalizerInterface $normalizer;

    private DenormalizerInterface $denormalizer;

    public function __construct(NormalizerInterface $normalizer, DenormalizerInterface $denormalizer)
    {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function encode(string $trackingId, object $message): string
    {
        $array = $this->normalizer->normalize(
            [
                'tracking_id' => $trackingId,
                'class' => get_class($message),
                'body' => $message,
            ]
        );

        return JSON::encode($array, JSON_UNESCAPED_SLASHES);
    }

    public function decode(string $encoded): DecodedMessage
    {
        $data = JSON::decode($encoded);

        $class = $data['class'] ?? '';
        if (!class_exists($class)) {
            throw new LogicException(sprintf('Event class "%s" not exists. Body: "%s"', $class, $encoded));
        }

        $event = $this->denormalizer->denormalize($data['body'], $class);
        if (!is_object($event)) {
            throw new LogicException(sprintf('Event class "%s" not exists. Body: "%s"', $class, $encoded));
        }

        return new DecodedMessage($event, $data['tracking_id']);
    }
}
