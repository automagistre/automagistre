<?php

declare(strict_types=1);

namespace App\Identifier\Doctrine;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

final class AutoTypeMappingListener implements EventSubscriberInterface
{
    public function __construct(private array $map = [])
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            \Doctrine\DBAL\Events::onSchemaColumnDefinition,
        ];
    }

    /**
     * @psalm-suppress PossiblyUndefinedArrayOffset
     * @psalm-suppress PropertyTypeCoercion
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        if ([] === $this->map) {
            return;
        }

        $metadata = $event->getClassMetadata();

        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldMapping = $metadata->getFieldMapping($fieldName);

            $type = $this->map[$metadata->getTableName()][$fieldMapping['columnName']] ?? null;

            if (null === $type) {
                continue;
            }

            $metadata->fieldMappings[$fieldName]['type'] = $type;
        }
    }

    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $event): void
    {
        $tableColumn = $event->getTableColumn();

        $type = $this->map[$event->getTable()][$tableColumn['field']] ?? null;

        if (null === $type) {
            return;
        }

        $column = new Column($tableColumn['field'], Type::getType($type), [
            'notnull' => (bool) $tableColumn['isnotnull'],
            'default' => $tableColumn['default'],
            'comment' => null,
        ]);

        $event->setColumn($column);
        $event->preventDefault();
    }
}
