<?php

declare(strict_types=1);

namespace App\Rest\Request;

use function class_exists;
use function is_string;
use function str_ends_with;
use function str_starts_with;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

final class DtoArgumentValueResolver implements ArgumentValueResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        return is_string($type)
            && class_exists($type)
            && str_starts_with($type, 'App\\')
            && str_ends_with($type, 'Dto');
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        /** @psalm-var class-string */
        $type = $argument->getType();

        yield $this->serializer->deserialize($request->getContent(), $type, 'json');
    }
}
