<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210911213501 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_34dcd176450ff010');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX uniq_34dcd176450ff010 ON person (telephone)');
    }
}
