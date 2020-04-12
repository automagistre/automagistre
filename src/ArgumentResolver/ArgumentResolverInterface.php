<?php

namespace App\ArgumentResolver;

use RuntimeException;

/**
 * @see https://github.com/symfony/symfony/issues/36290
 */
interface ArgumentResolverInterface
{
    /**
     * Returns the arguments to pass to the callable.
     *
     * @param mixed $data
     * @param callable $callable
     *
     * @return array An array of arguments to pass to the callable
     *
     * @throws RuntimeException When no value could be provided for a required argument
     */
    public function getArguments($data, $callable): array;
}
