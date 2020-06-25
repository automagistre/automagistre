<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625195955 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        // data migration
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT c.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, c.created_at
            FROM car c
        ');
        // data migration
        $this->addSql('ALTER TABLE car DROP created_at');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE car ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN car.created_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
