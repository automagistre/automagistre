<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200624185421 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
            INSERT INTO users (id, uuid, roles, username) 
            VALUES (20, \'1eab64c5-18b0-646c-9ac3-0242c0a8100a\'::UUID, \'[]\'::JSON, \'smsaero@automagistre.ru\')
            ON CONFLICT DO NOTHING 
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }
}
