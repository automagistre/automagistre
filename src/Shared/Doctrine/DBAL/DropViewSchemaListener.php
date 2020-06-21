<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\DBAL;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use function str_ends_with;

final class DropViewSchemaListener
{
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
