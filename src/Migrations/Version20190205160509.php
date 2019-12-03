<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190205160509 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE mc_equipment MODIFY COLUMN  equipment_engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type_enum)\', MODIFY COLUMN  equipment_transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission_enum)\', MODIFY COLUMN  equipment_wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('ALTER TABLE car MODIFY COLUMN equipment_transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission_enum)\', MODIFY COLUMN  equipment_wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive_enum)\', MODIFY COLUMN  equipment_engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type_enum)\'');

        $this->addSql('ALTER TABLE mc_equipment CHANGE equipment_engine_name equipment_engine_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE car CHANGE equipment_engine_name equipment_engine_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car CHANGE equipment_engine_name equipment_engine_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mc_equipment CHANGE equipment_engine_name equipment_engine_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
