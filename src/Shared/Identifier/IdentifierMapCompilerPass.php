<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function assert;
use function is_subclass_of;
use function method_exists;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IdentifierMapCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        /** @var ManagerRegistry $registry */
        $registry = $container->get('doctrine');

        $map = [];

        foreach ($registry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                assert($metadata instanceof ClassMetadataInfo);

                if ($metadata->isReadOnly) {
                    continue;
                }

                if ([] !== $metadata->getIdentifier()) {
                    $reflectionType = $metadata->getSingleIdReflectionProperty()->getType();

                    if (null === $reflectionType) {
                        continue;
                    }

                    /** @psalm-suppress RedundantCondition */
                    assert(method_exists($reflectionType, 'getName'));

                    /** @var string $identifierClass */
                    $identifierClass = $reflectionType->getName();

                    if (is_subclass_of($identifierClass, Identifier::class)) {
                        if (
                            $metadata->isInheritanceTypeNone()
                            || false === $metadata->getReflectionClass()->getParentClass()
                        ) {
                            $map[$identifierClass] = $metadata->getName();
                        }
                    }
                }
            }
        }

        $container->getDefinition(IdentifierMap::class)->addArgument($map);
    }
}
