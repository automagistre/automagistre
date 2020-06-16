<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200616142440 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT fk_b37ebc5fbb3453db');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT fk_b98f499c4ce34bec');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT fk_b98f499c70b9088c');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT fk_b37ebc5f517fe9fe');
        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT fk_2b65786f4d7b7542');
        $this->addSql('DROP SEQUENCE car_model_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE manufacturer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_equipment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_line_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_work_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE part_cross_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE part_case');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE stockpile');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_part');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('CREATE SEQUENCE car_model_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_equipment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_line_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_work_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE part_cross_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mc_work (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE manufacturer (id SERIAL NOT NULL, name VARCHAR(64) DEFAULT NULL, logo VARCHAR(25) DEFAULT NULL, localized_name VARCHAR(255) DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN manufacturer.uuid IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE part (id UUID NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(30) NOT NULL, universal BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_490f70c696901f54a23b42d ON part (number, manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN part.number IS \'(DC2Type:part_number)\'');
        $this->addSql('CREATE TABLE part_cross_part (part_cross_id INT NOT NULL, part_id UUID NOT NULL, PRIMARY KEY(part_cross_id, part_id))');
        $this->addSql('CREATE INDEX idx_b98f499c70b9088c ON part_cross_part (part_cross_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b98f499c4ce34bec ON part_cross_part (part_id)');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE part_case (id UUID NOT NULL, part_id UUID NOT NULL, vehicle_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a0e7894ce34bec545317d1 ON part_case (part_id, vehicle_id)');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, author VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, content TEXT NOT NULL, source VARCHAR(255) NOT NULL, publish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN review.publish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE stockpile (tenant SMALLINT NOT NULL, part_id UUID NOT NULL, quantity INT NOT NULL, PRIMARY KEY(part_id, tenant))');
        $this->addSql('CREATE UNIQUE INDEX uniq_c2e8923f4ce34bec4e59c462 ON stockpile (part_id, tenant)');
        $this->addSql('CREATE INDEX idx_c2e8923f4ce34bec4e59c4629ff31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('COMMENT ON COLUMN stockpile.tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN stockpile.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE car_model (id SERIAL NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, localized_name VARCHAR(255) DEFAULT NULL, case_name VARCHAR(255) DEFAULT NULL, year_from SMALLINT DEFAULT NULL, year_till SMALLINT DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_83ef70ed17f50a6 ON car_model (uuid)');
        $this->addSql('CREATE UNIQUE INDEX uniq_83ef70ea23b42ddf3ba4b5 ON car_model (manufacturer_id, case_name)');
        $this->addSql('COMMENT ON COLUMN car_model.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE part_cross (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mc_equipment (id SERIAL NOT NULL, equipment_engine_name VARCHAR(255) DEFAULT NULL, equipment_engine_capacity VARCHAR(255) NOT NULL, equipment_transmission SMALLINT NOT NULL, equipment_wheel_drive SMALLINT NOT NULL, period INT NOT NULL, equipment_engine_type SMALLINT NOT NULL, equipment_engine_air_intake SMALLINT DEFAULT NULL, equipment_engine_injection SMALLINT DEFAULT NULL, uuid UUID NOT NULL, vehicle_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.uuid IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE mc_line (id SERIAL NOT NULL, equipment_id INT DEFAULT NULL, work_id INT DEFAULT NULL, period INT NOT NULL, recommended BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b37ebc5fbb3453db ON mc_line (work_id)');
        $this->addSql('CREATE INDEX idx_b37ebc5f517fe9fe ON mc_line (equipment_id)');
        $this->addSql('CREATE TABLE mc_part (id SERIAL NOT NULL, line_id INT DEFAULT NULL, quantity INT NOT NULL, recommended BOOLEAN NOT NULL, part_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2b65786f4d7b7542 ON mc_part (line_id)');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT fk_b98f499c4ce34bec FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT fk_b98f499c70b9088c FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON UPDATE RESTRICT ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT fk_b37ebc5f517fe9fe FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT fk_b37ebc5fbb3453db FOREIGN KEY (work_id) REFERENCES mc_work (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT fk_2b65786f4d7b7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
