<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200621232125 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE manufacturer_id_seq CASCADE');
        $this->addSql('ALTER TABLE manufacturer DROP id');
        $this->addSql('ALTER TABLE manufacturer RENAME uuid TO id');
        $this->addSql('ALTER TABLE manufacturer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE manufacturer ADD PRIMARY KEY (id)');
        $this->addSql('COMMENT ON COLUMN manufacturer.id IS \'(DC2Type:manufacturer_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE manufacturer ADD uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE manufacturer ALTER id TYPE INT');
        $this->addSql('ALTER TABLE manufacturer ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE manufacturer_id_seq');
        $this->addSql('SELECT setval(\'manufacturer_id_seq\', (SELECT MAX(id) FROM manufacturer))');
        $this->addSql('ALTER TABLE manufacturer ALTER id SET DEFAULT nextval(\'manufacturer_id_seq\')');
        $this->addSql('COMMENT ON COLUMN manufacturer.uuid IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN manufacturer.id IS NULL');
    }
}
