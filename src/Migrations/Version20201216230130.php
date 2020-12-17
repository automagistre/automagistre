<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201216230130 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE car SET equipment_engine_air_intake = 0 WHERE equipment_engine_air_intake IS NULL');
        $this->addSql('UPDATE car SET equipment_engine_injection = 0 WHERE equipment_engine_injection IS NULL');
        $this->addSql('UPDATE mc_equipment SET equipment_engine_air_intake = 0 WHERE equipment_engine_air_intake IS NULL');
        $this->addSql('UPDATE mc_equipment SET equipment_engine_injection = 0 WHERE equipment_engine_injection IS NULL');

        $this->addSql('ALTER TABLE car ALTER equipment_engine_air_intake SET NOT NULL');
        $this->addSql('ALTER TABLE car ALTER equipment_engine_injection SET NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER equipment_engine_air_intake SET NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER equipment_engine_injection SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE car ALTER equipment_engine_air_intake DROP NOT NULL');
        $this->addSql('ALTER TABLE car ALTER equipment_engine_injection DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER equipment_engine_air_intake DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER equipment_engine_injection DROP NOT NULL');
    }
}
