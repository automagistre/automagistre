<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210707171825 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part ADD warehouse_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN part.warehouse_id IS \'(DC2Type:warehouse_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part DROP warehouse_id');
    }
}
