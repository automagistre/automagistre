<?php

declare(strict_types=1);

namespace App\EasyAdmin\Request;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use function is_object;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformerArgumentValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(private EntityTransformer $entityTransformer)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return null !== $argument->getType() && is_object($this->resolve($request, $argument)->current());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var string $class */
        $class = $argument->getType();

        yield $this->entityTransformer->reverseTransform($class, $request);
    }
}
