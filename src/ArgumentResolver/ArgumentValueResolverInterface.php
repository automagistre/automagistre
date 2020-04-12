<?php

namespace App\ArgumentResolver;

use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @see https://github.com/symfony/symfony/issues/36290
 */
interface ArgumentValueResolverInterface
{
    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     *
     * @param mixed $data
     */
    public function supports($data, ArgumentMetadata $argument): bool;

    /**
     * Returns the possible value(s).
     *
     * @param mixed $data
     */
    public function resolve($data, ArgumentMetadata $argument): iterable;
}
