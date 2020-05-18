<?php

declare(strict_types=1);

namespace App\JSONRPC;

use App\Shared\ArgumentResolver\ArgumentValueResolverInterface;
use function array_map;
use function assert;
use function class_exists;
use function count;
use function in_array;
use function is_string;
use function iterator_to_array;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CommandArgumentValueResolver implements ArgumentValueResolverInterface
{
    private DenormalizerInterface $denormalizer;

    private ValidatorInterface $validator;

    private TranslatorInterface $translator;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        TranslatorInterface $translator
    ) {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
        $this->translator = $translator;
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

        try {
            $dto = $this->denormalizer->denormalize($data, $class, null, $context);
        } catch (NotNormalizableValueException $e) {
            throw new ArgumentException(['message' => $e->getMessage()]);
        }

        $errors = $this->validator->validate($dto);
        if (0 !== count($errors)) {
            throw new ArgumentException(array_map(fn (ConstraintViolationInterface $violation) => [
                'field' => $violation->getPropertyPath(),
                'message' => $this->translator->trans(
                    $violation->getMessageTemplate(),
                    $violation->getParameters(),
                    'validators'
                ),
            ], iterator_to_array($errors)));
        }

        yield $dto;
    }
}
