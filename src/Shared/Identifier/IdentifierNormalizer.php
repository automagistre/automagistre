<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use function assert;
use function is_subclass_of;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class IdentifierNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        assert(is_subclass_of($type, Identifier::class));

        return Identifier::fromClass($type, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_subclass_of($type, Identifier::class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        assert($object instanceof Identifier);

        return $object->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Identifier;
    }
}
