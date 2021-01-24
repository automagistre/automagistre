<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Listeners;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MetadataCacheCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ('prod' !== $container->getParameter('kernel.environment')) {
            return;
        }

        $metadataCache = $container->get('doctrine.metadata_cache.phparray_adapter');
        assert($metadataCache instanceof PhpArrayAdapter);

        $fallbackCache = $container->get('doctrine.metadata_cache.array_adapter');
        assert($fallbackCache instanceof ArrayAdapter);

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        foreach ($doctrine->getManagers() as $manager) {
            $manager->getMetadataFactory()->getAllMetadata();
        }

        $metadataCache->warmUp($fallbackCache->getValues());

        $container->getDefinition('doctrine.metadata_cache.phparray_adapter')
            ->replaceArgument(1, new Reference('doctrine.metadata_cache.apcu_adapter'))
        ;
        $container->removeDefinition('doctrine.metadata_cache.array_adapter');
    }
}
