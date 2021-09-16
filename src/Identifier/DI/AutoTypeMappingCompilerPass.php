<?php

declare(strict_types=1);

namespace App\Identifier\DI;

use App\Identifier\Doctrine\AutoTypeMappingListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function array_key_exists;
use function assert;
use function is_string;

final class AutoTypeMappingCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    public function process(ContainerBuilder $container): void
    {
        $identifiers = array_flip($container->getDefinition(TypeRegistrator::class)->getArgument(0));

        $isSupportType = static function (?ReflectionType $ref) use ($identifiers): ?string {
            if (!$ref instanceof ReflectionNamedType) {
                return null;
            }

            $type = $ref->getName();

            if (UuidInterface::class === $type) {
                return 'uuid';
            }

            if (array_key_exists($type, $identifiers)) {
                return $type;
            }

            return null;
        };

        /** @var Registry $registry */
        $registry = $container->get('doctrine');

        $map = [];

        foreach ($registry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                assert($metadata instanceof ClassMetadata);

                $baseRefClass = $metadata->getReflectionClass();

                foreach ($metadata->getFieldNames() as $fieldName) {
                    $fieldMapping = $metadata->getFieldMapping($fieldName);

                    if (str_contains($fieldName, '.')) {
                        $embeddedRef = new ReflectionClass($fieldMapping['originalClass']);
                        $embeddedPropertyRef = $embeddedRef->getProperty($fieldMapping['originalField']);

                        if (is_string($type = $isSupportType($embeddedPropertyRef->getType()))) {
                            $map[$metadata->getTableName()][$fieldMapping['columnName']] = $type;
                        }

                        continue;
                    }

                    $refClass = $baseRefClass;
                    while (!$refClass->hasProperty($fieldName)) {
                        $refClass = $refClass->getParentClass();

                        if (false === $refClass) {
                            continue 2;
                        }
                    }

                    $refProperty = $refClass->getProperty($fieldName);

                    if (is_string($type = $isSupportType($refProperty->getType()))) {
                        $metadata->fieldMappings[$fieldName]['type'] = $type;
                        $map[$metadata->getTableName()][$fieldMapping['columnName']] = $type;
                    }
                }

                foreach ($metadata->associationMappings as $association) {
                    $baseTargetRefClass = new ReflectionClass($association['targetEntity']);

                    if (!array_key_exists('joinColumns', $association)) {
                        continue;
                    }

                    foreach ($association['joinColumns'] as $joinColumn) {
                        $targetRefClass = $baseTargetRefClass;

                        while (!$targetRefClass->hasProperty($joinColumn['referencedColumnName'])) {
                            $targetRefClass = $targetRefClass->getParentClass();

                            if (false === $targetRefClass) {
                                continue 2;
                            }
                        }

                        $targetRefProperty = $targetRefClass->getProperty($joinColumn['referencedColumnName']);

                        if (is_string($type = $isSupportType($targetRefProperty->getType()))) {
                            $map[$metadata->getTableName()][$joinColumn['name']] = $type;
                        }
                    }
                }
            }

            $container->getDefinition(AutoTypeMappingListener::class)->setArgument(0, $map);
        }
    }
}
