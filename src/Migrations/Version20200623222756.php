<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200623222756 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE stockpile');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE stockpile (part_id UUID NOT NULL, tenant SMALLINT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(part_id, tenant))');
        $this->addSql('CREATE INDEX idx_c2e8923f4ce34bec4e59c4629ff31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('CREATE UNIQUE INDEX uniq_c2e8923f4ce34bec4e59c462 ON stockpile (part_id, tenant)');
        $this->addSql('COMMENT ON COLUMN stockpile.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN stockpile.tenant IS \'(DC2Type:tenant_enum)\'');
    }
}
