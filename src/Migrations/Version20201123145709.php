<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123145709 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ADD note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE appeal_calculator ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE appeal_calculator ADD equipment_id UUID NOT NULL');
        $this->addSql('ALTER TABLE appeal_calculator ADD mileage INT NOT NULL');
        $this->addSql('ALTER TABLE appeal_calculator ADD total VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE appeal_calculator RENAME COLUMN body TO works');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.equipment_id IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.total IS \'(DC2Type:money)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator DROP note');
        $this->addSql('ALTER TABLE appeal_calculator DROP date');
        $this->addSql('ALTER TABLE appeal_calculator DROP equipment_id');
        $this->addSql('ALTER TABLE appeal_calculator DROP mileage');
        $this->addSql('ALTER TABLE appeal_calculator DROP total');
        $this->addSql('ALTER TABLE appeal_calculator RENAME COLUMN works TO body');
    }
}
