<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Entity\Embeddable\Relation;
use App\Tenant\EventListener\TenantRelationListener;
use function assert;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use function is_subclass_of;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MetadataCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->get('doctrine');

        $map = [];
        foreach ($registry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                assert($metadata instanceof ClassMetadataInfo);

                foreach ($metadata->embeddedClasses as $property => $embedded) {
                    $embeddedClass = $embedded['class'];

                    if (is_subclass_of($embeddedClass, Relation::class, true)) {
                        $map[$metadata->getName()][$property] = $embeddedClass;
                    }
                }
            }
        }

        $container->getDefinition(TenantRelationListener::class)->setArgument(1, $map);

        if ('prod' !== $container->getParameter('kernel.environment')) {
            return;
        }

        $metadataCache = $container->get('doctrine.metadata_cache.phparray_adapter');
        assert($metadataCache instanceof PhpArrayAdapter);

        $fallbackCache = $container->get('doctrine.metadata_cache.array_adapter');
        assert($fallbackCache instanceof ArrayAdapter);

        $metadataCache->warmUp($fallbackCache->getValues());

        $container->getDefinition('doctrine.metadata_cache.phparray_adapter')
            ->replaceArgument(1, new Reference('doctrine.metadata_cache.apcu_adapter'));
        $container->removeDefinition('doctrine.metadata_cache.array_adapter');
    }
}
