<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200528094027 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry_deletion ADD id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry_deletion ALTER id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F118663DBA364942 ON calendar_entry_deletion (entry_id)');
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP CONSTRAINT calendar_entry_deletion_pkey');
        $this->addSql('ALTER TABLE calendar_entry_deletion ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP INDEX UNIQ_F118663DBA364942');
        $this->addSql('DROP INDEX calendar_entry_deletion_pkey');
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP id');
        $this->addSql('ALTER TABLE calendar_entry_deletion ALTER entry_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_deletion ADD PRIMARY KEY (entry_id)');
    }
}
