<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625200329 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE motion ADD uuid UUID DEFAULT NULL');
        $this->addSql('DROP INDEX idx_f5fea1e84ce34bec8b8e8428');
        $this->addSql('ALTER TABLE motion DROP created_at');
        $this->addSql('ALTER TABLE motion DROP id');
        $this->addSql('ALTER TABLE motion RENAME uuid TO id');
        $this->addSql('ALTER TABLE motion ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE motion ADD PRIMARY KEY (id)');
        $this->addSql('COMMENT ON COLUMN motion.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE motion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE motion ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER id TYPE INT');
        $this->addSql('ALTER TABLE motion ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE motion_id_seq');
        $this->addSql('SELECT setval(\'motion_id_seq\', (SELECT MAX(id) FROM motion))');
        $this->addSql('ALTER TABLE motion ALTER id SET DEFAULT nextval(\'motion_id_seq\')');
        $this->addSql('COMMENT ON COLUMN motion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN motion.id IS NULL');
        $this->addSql('CREATE INDEX idx_f5fea1e84ce34bec8b8e8428 ON motion (part_id, created_at)');
    }
}
