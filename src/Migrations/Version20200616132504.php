<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200616132504 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_equipment DROP CONSTRAINT fk_793047587975b7e7');
        $this->addSql('DROP INDEX idx_793047587975b7e7');
        $this->addSql('ALTER TABLE mc_equipment ADD vehicle_id UUID DEFAULT NULL');
        $this->addSql('UPDATE mc_equipment me SET vehicle_id = (SELECT uuid FROM car_model cm WHERE me.model_id = cm.id)');
        $this->addSql('ALTER TABLE mc_equipment DROP model_id');
        $this->addSql('COMMENT ON COLUMN mc_equipment.vehicle_id IS \'(DC2Type:vehicle_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('Nope.');
    }
}
