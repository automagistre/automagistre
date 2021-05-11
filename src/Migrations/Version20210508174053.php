<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210508174053 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(sprintf(
            'DELETE FROM migration_versions WHERE version NOT IN (\'%s\', \'%s\')',
            Version20210508162313::class,
            self::class,
        ));
    }
}
