<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190205094832 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('ALTER TABLE mc_equipment CHANGE engine equipment_engine_name VARCHAR(255) NOT NULL, ADD equipment_engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type)\', CHANGE engine_capacity equipment_engine_capacity VARCHAR(255) NOT NULL, CHANGE transmission equipment_transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', CHANGE wheel_drive equipment_wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\'');

        $this->addSql('UPDATE car SET engine_capacity = \'0\' WHERE engine_capacity IS NULL');
        $this->addSql('ALTER TABLE car ADD equipment_engine_name VARCHAR(255) NOT NULL, CHANGE engine_capacity equipment_engine_capacity VARCHAR(255) NOT NULL, CHANGE transmission equipment_transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', CHANGE wheel_drive equipment_wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\', CHANGE engine_type equipment_engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        throw new \LogicException('Fiasco');
    }
}
