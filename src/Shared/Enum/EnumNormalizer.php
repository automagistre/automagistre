<?php

declare(strict_types=1);

namespace App\Shared\Enum;

use Premier\Enum\Enum;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function is_int;
use function is_subclass_of;

final class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Enum
    {
        /** @var callable $callable */
        $callable = [$type, 'create'];

        return $callable($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_int($data) && is_subclass_of($type, Enum::class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = []): int
    {
        /** @var Enum $object */

        return $object->toId();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Enum;
    }
}
