<?php

declare(strict_types=1);

namespace App\Command\Migrations;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MigrateCommand extends MigrationsMigrateDoctrineCommand
{
    use DoctrineCommandHelpers;
}
