<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EasyAdminArgumentValueResolver implements ArgumentValueResolverInterface
{
    const ATTRIBUTE = 'easyadmin_arguments';

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $arguments = $request->attributes->get(self::ATTRIBUTE);

        return !empty($arguments) && array_key_exists($argument->getName(), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $request->attributes->get(self::ATTRIBUTE)[$argument->getName()];
    }
}
