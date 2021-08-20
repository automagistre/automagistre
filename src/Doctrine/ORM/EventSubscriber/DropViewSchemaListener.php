<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use function str_ends_with;

final class DropViewSchemaListener implements EventSubscriberInterface
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
            if (str_ends_with($table->getName(), '_view')) {
                $schema->dropTable($table->getName());
            }
        }
    }
}
