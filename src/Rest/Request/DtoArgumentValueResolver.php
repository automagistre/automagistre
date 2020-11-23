<?php

declare(strict_types=1);

namespace App\Rest\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

final class DtoArgumentValueResolver implements ArgumentValueResolverInterface
{
    private DtoDetector $detector;

    private SerializerInterface $serializer;

    public function __construct(DtoDetector $detector, SerializerInterface $serializer)
    {
        $this->detector = $detector;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $this->detector->isDto($argument->getType());
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
