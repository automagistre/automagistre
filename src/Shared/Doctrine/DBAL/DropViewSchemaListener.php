<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\DBAL;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

final class DropViewSchemaListener
{
    private const VIEWS = [
        'calendar_entry_view',
    ];

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        foreach (self::VIEWS as $view) {
            if ($schema->hasTable($view)) {
                $schema->dropTable($view);
            }
        }
    }
}
