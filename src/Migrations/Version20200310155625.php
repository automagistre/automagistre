<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200310155625 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('DROP INDEX idx_f5fea1e84ce34bec');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC8B8E8428 ON motion (part_id, created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('DROP INDEX IDX_F5FEA1E84CE34BEC8B8E8428');
        $this->addSql('CREATE INDEX idx_f5fea1e84ce34bec ON motion (part_id)');
    }
}
