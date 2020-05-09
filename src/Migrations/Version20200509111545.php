<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200509111545 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE created_by (
          id UUID NOT NULL, 
          user_id UUID DEFAULT NULL, 
          created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN created_by.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN created_by.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN created_by.created_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE created_by');
    }
}
