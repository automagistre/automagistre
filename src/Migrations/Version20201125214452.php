<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201125214452 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ALTER date DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER body_type DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER diameter DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ALTER date SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER body_type SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER diameter SET NOT NULL');
    }
}
