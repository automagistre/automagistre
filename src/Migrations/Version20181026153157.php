<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181026153157 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_equipment CHANGE transmission transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission_enum)\', CHANGE wheel_drive wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive_enum)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
