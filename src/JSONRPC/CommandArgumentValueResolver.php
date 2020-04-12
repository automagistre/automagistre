<?php

declare(strict_types=1);

namespace App\JSONRPC;

use App\ArgumentResolver\ArgumentValueResolverInterface;
use function assert;
use function class_exists;
use function count;
use function in_array;
use function is_string;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CommandArgumentValueResolver implements ArgumentValueResolverInterface
{
    private DenormalizerInterface $denormalizer;

    private ValidatorInterface $validator;

    public function __construct(DenormalizerInterface $denormalizer, ValidatorInterface $validator)
    {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, ArgumentMetadata $argument): bool
    {
        return in_array($argument->getName(), ['command', 'query'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($data, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        assert(is_string($class) && class_exists($class));

        $context = [
            'disable_type_enforcement' => true,
            'allow_extra_attributes' => true,
        ];

        $dto = $this->denormalizer->denormalize($data, $class, null, $context);

        $errors = $this->validator->validate($dto);
        if (0 !== count($errors)) {
            throw new ValidationException($errors);
        }

        yield $dto;
    }
}
