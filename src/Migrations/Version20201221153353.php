<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201221153353 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE appeal_tire_fitting SET body_type = 0');
        $this->addSql('ALTER TABLE appeal_tire_fitting RENAME body_type TO category');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER category SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.category IS \'(DC2Type:carcase_enum)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_tire_fitting RENAME category TO body_type');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.body_type IS \'(DC2Type:carcase_enum)\'');
    }
}
