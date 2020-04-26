<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200426201608 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_model ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_model ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:vehicle_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_model ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_model ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:uuid)\'');
    }
}
