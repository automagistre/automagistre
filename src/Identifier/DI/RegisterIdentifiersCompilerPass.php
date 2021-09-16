<?php

declare(strict_types=1);

namespace App\Identifier\DI;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function count;

final class RegisterIdentifiersCompilerPass implements CompilerPassInterface
{
    public const TAG = 'identifier.class';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registrator = $container->getDefinition(TypeRegistrator::class);
        $registrator->setArgument(0, array_keys($container->findTaggedServiceIds(self::TAG)));

        $definition = $container->getDefinition('doctrine');
        $definition->setArgument(count($definition->getArguments()), new Reference(TypeRegistrator::class));
    }
}
