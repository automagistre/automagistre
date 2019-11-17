<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Entity\Embeddable\Relation;
use App\Tenant\EventListener\TenantRelationListener;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $registry = $container->get(RegistryInterface::class);

        $map = [];
        foreach ($registry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                \assert($metadata instanceof ClassMetadataInfo);

                foreach ($metadata->embeddedClasses as $property => $embedded) {
                    $embeddedClass = $embedded['class'];

                    if (\is_subclass_of($embeddedClass, Relation::class, true)) {
                        $map[$metadata->getName()][$property] = $embeddedClass;
                    }
                }
            }
        }

        $container->getDefinition(TenantRelationListener::class)->setArgument(1, $map);
    }
}
