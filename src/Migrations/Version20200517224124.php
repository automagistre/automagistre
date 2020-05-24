<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200517224124 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry DROP created_at');
        $this->addSql('ALTER TABLE calendar_entry DROP created_by');
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP deleted_by');
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry_deletion ADD deleted_by UUID NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_deletion ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.deleted_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE calendar_entry ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD created_by UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.created_by IS \'(DC2Type:user_id)\'');
    }
}
