<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201230201135 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ALTER works TYPE JSON');
        $this->addSql('ALTER TABLE appeal_calculator ALTER works DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.works IS \'(DC2Type:appeal_calculator_work)\'');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER works TYPE JSON');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER works DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.works IS \'(DC2Type:appeal_tire_fitting_work)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ALTER works TYPE JSON');
        $this->addSql('ALTER TABLE appeal_calculator ALTER works DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.works IS NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER works TYPE JSON');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER works DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.works IS NULL');
    }
}
