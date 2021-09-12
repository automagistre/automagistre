<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

final class DropForeignKeyToViewsListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema,
        ];
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        foreach ($schema->getTables() as $table) {
            foreach ($table->getForeignKeys() as $foreignKey) {
                if (str_ends_with($foreignKey->getForeignTableName(), '_view')) {
                    $table->removeForeignKey($foreignKey->getName());
                }
            }
        }
    }
}
