<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200624155557 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE car_recommendation DROP realization_tenant');
        $this->addSql('ALTER TABLE car_recommendation RENAME COLUMN realization_id TO realization');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE car_recommendation ADD realization_tenant SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation RENAME COLUMN realization TO realization_id');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization_tenant IS \'(DC2Type:tenant_enum)\'');
    }
}
