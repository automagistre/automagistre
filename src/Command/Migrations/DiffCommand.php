<?php

declare(strict_types=1);

namespace App\Command\Migrations;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsDiffDoctrineCommand;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DiffCommand extends MigrationsDiffDoctrineCommand
{
    use DoctrineCommandHelpers;
}
