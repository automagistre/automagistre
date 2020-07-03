<?php

declare(strict_types=1);

namespace App\SimpleBus\DI;

use App\SimpleBus\AsyncEventBusMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AsyncEventMiddlewareCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('simple_bus.event_bus')
            ->addArgument([
                new Reference(AsyncEventBusMiddleware::class),
            ]);

        $container->getDefinition('simple_bus.command_bus')
            ->addArgument([
                new Reference(AsyncEventBusMiddleware::class),
            ]);
    }
}
