<?php

declare(strict_types=1);

namespace App\Uuid;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param UuidInterface $object
     * @param null          $format
     * @param array         $context
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        return $object->toString();
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof UuidInterface;
    }

    public function denormalize($data, $class, $format = null, array $context = []): UuidInterface
    {
        if (!Uuid::isValid($data)) {
            throw new InvalidArgumentException(sprintf('"%s" is not valid Uuid', $data));
        }

        return Uuid::fromString($data);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $type === UuidInterface::class;
    }
}
