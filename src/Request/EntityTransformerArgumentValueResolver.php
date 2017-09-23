<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformerArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var EntityTransformer
     */
    private $entityTransformer;

    public function __construct(EntityTransformer $entityTransformer)
    {
        $this->entityTransformer = $entityTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() && $this->resolve($request, $argument)->current();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->entityTransformer->reverseTransform($argument->getType(), $request);
    }
}
