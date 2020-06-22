<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200621225826 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users ADD first_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP person_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users ADD person_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP first_name');
        $this->addSql('ALTER TABLE users DROP last_name');
        $this->addSql('COMMENT ON COLUMN users.person_id IS \'(DC2Type:operand_id)\'');
    }
}
