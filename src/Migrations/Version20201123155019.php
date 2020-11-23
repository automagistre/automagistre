<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123155019 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_tire_fitting ADD model_id UUID NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ADD body_type SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ADD diameter INT NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ADD total VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting RENAME COLUMN body TO works');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.model_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.body_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.total IS \'(DC2Type:money)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_tire_fitting DROP model_id');
        $this->addSql('ALTER TABLE appeal_tire_fitting DROP body_type');
        $this->addSql('ALTER TABLE appeal_tire_fitting DROP diameter');
        $this->addSql('ALTER TABLE appeal_tire_fitting DROP total');
        $this->addSql('ALTER TABLE appeal_tire_fitting RENAME COLUMN works TO body');
    }
}
