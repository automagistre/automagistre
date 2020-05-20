<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200520211442 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_equipment ADD equipment_engine_air_intake SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_equipment ADD equipment_engine_injection SMALLINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_equipment DROP equipment_engine_air_intake');
        $this->addSql('ALTER TABLE mc_equipment DROP equipment_engine_injection');
    }
}
