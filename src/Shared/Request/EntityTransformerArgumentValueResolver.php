<?php

declare(strict_types=1);

namespace App\Shared\Request;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function is_object;
use function trim;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformerArgumentValueResolver implements ArgumentValueResolverInterface
{
    private EntityTransformer $entityTransformer;

    public function __construct(EntityTransformer $entityTransformer)
    {
        $this->entityTransformer = $entityTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return '' !== trim($argument->getType()) && is_object($this->resolve($request, $argument)->current());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->entityTransformer->reverseTransform($argument->getType(), $request);
    }
}
