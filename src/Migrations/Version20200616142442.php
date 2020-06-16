<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200616142442 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE mc_equipment (id SERIAL NOT NULL, uuid UUID NOT NULL, vehicle_id UUID DEFAULT NULL, period INT NOT NULL, equipment_transmission SMALLINT NOT NULL, equipment_wheel_drive SMALLINT NOT NULL, equipment_engine_name VARCHAR(255) DEFAULT NULL, equipment_engine_type SMALLINT NOT NULL, equipment_engine_air_intake SMALLINT DEFAULT NULL, equipment_engine_injection SMALLINT DEFAULT NULL, equipment_engine_capacity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mc_equipment.uuid IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('CREATE TABLE mc_line (id SERIAL NOT NULL, equipment_id INT DEFAULT NULL, work_id INT DEFAULT NULL, period INT NOT NULL, recommended BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B37EBC5F517FE9FE ON mc_line (equipment_id)');
        $this->addSql('CREATE INDEX IDX_B37EBC5FBB3453DB ON mc_line (work_id)');
        $this->addSql('CREATE TABLE mc_part (id SERIAL NOT NULL, line_id INT DEFAULT NULL, part_id UUID NOT NULL, quantity INT NOT NULL, recommended BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B65786F4D7B7542 ON mc_part (line_id)');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE mc_work (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE manufacturer (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(64) DEFAULT NULL, localized_name VARCHAR(255) DEFAULT NULL, logo VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN manufacturer.uuid IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE part (id UUID NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(30) NOT NULL, universal BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D ON part (number, manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN part.number IS \'(DC2Type:part_number)\'');
        $this->addSql('CREATE TABLE part_case (id UUID NOT NULL, part_id UUID NOT NULL, vehicle_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D1 ON part_case (part_id, vehicle_id)');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE part_cross (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE part_cross_part (part_cross_id INT NOT NULL, part_id UUID NOT NULL, PRIMARY KEY(part_cross_id, part_id))');
        $this->addSql('CREATE INDEX IDX_B98F499C70B9088C ON part_cross_part (part_cross_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B98F499C4CE34BEC ON part_cross_part (part_id)');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE stockpile (part_id UUID NOT NULL, tenant SMALLINT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(part_id, tenant))');
        $this->addSql('CREATE INDEX IDX_C2E8923F4CE34BEC4E59C4629FF31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2E8923F4CE34BEC4E59C462 ON stockpile (part_id, tenant)');
        $this->addSql('COMMENT ON COLUMN stockpile.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN stockpile.tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, author VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, content TEXT NOT NULL, source VARCHAR(255) NOT NULL, publish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN review.publish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE vehicle_model (id SERIAL NOT NULL, uuid UUID NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, localized_name VARCHAR(255) DEFAULT NULL, case_name VARCHAR(255) DEFAULT NULL, year_from SMALLINT DEFAULT NULL, year_till SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235D17F50A6 ON vehicle_model (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42DDF3BA4B5 ON vehicle_model (manufacturer_id, case_name)');
        $this->addSql('COMMENT ON COLUMN vehicle_model.uuid IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN vehicle_model.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5F517FE9FE');
        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT FK_2B65786F4D7B7542');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT FK_B98F499C4CE34BEC');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT FK_B98F499C70B9088C');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_part');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_case');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE stockpile');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE vehicle_model');
    }
}
