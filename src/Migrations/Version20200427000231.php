<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200427000231 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car DROP CONSTRAINT fk_773de69d7975b7e7');
        $this->addSql('DROP INDEX idx_773de69d7975b7e7');
        $this->addSql('ALTER TABLE car ADD vehicle_id UUID DEFAULT NULL');
        $this->addSql('UPDATE car t SET vehicle_id = v.uuid FROM (SELECT id, uuid FROM car_model) AS v WHERE t.model_id = v.id');
        $this->addSql('ALTER TABLE car DROP model_id');
        $this->addSql('COMMENT ON COLUMN car.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car ADD model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car DROP vehicle_id');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT fk_773de69d7975b7e7 FOREIGN KEY (model_id) REFERENCES car_model (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_773de69d7975b7e7 ON car (model_id)');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id DROP NOT NULL');
    }
}
