<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

final class HasuraSchemaListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema,
            Events::onSchemaColumnDefinition,
        ];
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        $schema->getTable('tenant')
            ->addForeignKeyConstraint('tenant_group', ['group_id'], ['id'], [], 'tenant_group_id_fkey;')
            ->addUniqueIndex(['id'], 'temporal_unique_idx')
        ;
        $schema->getTable('tenant_permission')->addForeignKeyConstraint(
            'tenant',
            ['tenant_id'],
            ['id'],
            [],
            'tenant_permission_tenant_id_fkey',
        );
    }

    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $event): void
    {
        if ('created_by' === $event->getTable()) {
            return;
        }

        $tableColumn = $event->getTableColumn();

        if ('timestamptz' === $tableColumn['type']) {
            $event->setColumn();
            $event->preventDefault();
        }
    }
}
