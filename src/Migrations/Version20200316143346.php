<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200316143346 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE TABLE calendar_entry_deletion (
          id UUID NOT NULL, 
          entry_id UUID DEFAULT NULL, 
          reason smallint NOT NULL, 
          description TEXT DEFAULT NULL, 
          deleted_by UUID NOT NULL, 
          deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F118663DBA364942 ON calendar_entry_deletion (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.reason IS \'(DC2Type:deletion_reason)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.deleted_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE 
          calendar_entry_deletion 
        ADD 
          CONSTRAINT FK_F118663DBA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          calendar_entry 
        ADD 
          CONSTRAINT FK_47759E1E2CF9DDC FOREIGN KEY (previous) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47759E1E2CF9DDC ON calendar_entry (previous)');
        $this->addSql('DROP INDEX idx_f118663dba364942');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F118663DBA364942 ON calendar_entry_deletion (entry_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('DROP TABLE calendar_entry_deletion');
        $this->addSql('ALTER TABLE calendar_entry DROP CONSTRAINT FK_47759E1E2CF9DDC');
        $this->addSql('DROP INDEX UNIQ_47759E1E2CF9DDC');
        $this->addSql('DROP INDEX UNIQ_F118663DBA364942');
        $this->addSql('CREATE INDEX idx_f118663dba364942 ON calendar_entry_deletion (entry_id)');
    }
}
