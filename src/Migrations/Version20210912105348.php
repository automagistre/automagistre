<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912105348 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tenant_group (id UUID NOT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tenant_group.id IS \'(DC2Type:tenant_group_id)\'');
        $this->addSql('CREATE TABLE tenant (
          id UUID NOT NULL,
          group_id UUID NOT NULL,
          identifier VARCHAR(255) NOT NULL,
          display_name VARCHAR(255) NOT NULL,
          PRIMARY KEY(id, group_id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E59C462772E836A ON tenant (identifier)');
        $this->addSql('COMMENT ON COLUMN tenant.id IS \'(DC2Type:tenant_id)\'');
        $this->addSql('COMMENT ON COLUMN tenant.group_id IS \'(DC2Type:tenant_group_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tenant_group');
        $this->addSql('DROP TABLE tenant');
    }
}
