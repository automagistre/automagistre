<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200512235242 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_note DROP CONSTRAINT IF EXISTS fk_4d7eeb8c3c6f69f');
        $this->addSql('ALTER TABLE car_recommendation DROP CONSTRAINT IF EXISTS fk_3486230cc3c6f69f');
        $this->addSql('ALTER TABLE balance DROP CONSTRAINT IF EXISTS fk_acf41ffe18d7f226');
        $this->addSql('ALTER TABLE car DROP CONSTRAINT IF EXISTS fk_773de69d7e3c61f9');
        $this->addSql('ALTER TABLE operand_note DROP CONSTRAINT IF EXISTS fk_36bde44118d7f226');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT IF EXISTS fk_c1ee637cbf396750');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT IF EXISTS fk_34dcd176bf396750');
        $this->addSql('ALTER TABLE car_recommendation_part DROP CONSTRAINT IF EXISTS fk_ddc72d65d173940b');
        $this->addSql('DROP SEQUENCE car_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE car_note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE car_recommendation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE car_recommendation_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE operand_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE operand_note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE balance_id_seq CASCADE');
        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP INDEX uniq_acf41ffe18d7f2264e59c462');
        $this->addSql('DROP INDEX idx_acf41ffe18d7f226');
        $this->addSql('DROP TABLE balance');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('CREATE SEQUENCE car_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_recommendation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_recommendation_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE car_note (
          id SERIAL NOT NULL, 
          car_id INT DEFAULT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_4d7eeb8c3c6f69f ON car_note (car_id)');
        $this->addSql('COMMENT ON COLUMN car_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE car_recommendation_part (
          id SERIAL NOT NULL, 
          recommendation_id INT DEFAULT NULL, 
          quantity INT NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          uuid UUID NOT NULL, 
          created_by UUID NOT NULL, 
          part_id UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_ddc72d65d173940b ON car_recommendation_part (recommendation_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_ddc72d65d17f50a6 ON car_recommendation_part (uuid)');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:recommendation_part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE car (
          id SERIAL NOT NULL, 
          owner_id INT DEFAULT NULL, 
          identifier VARCHAR(17) DEFAULT NULL, 
          year INT DEFAULT NULL, 
          gosnomer VARCHAR(255) DEFAULT NULL, 
          description TEXT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          equipment_engine_type SMALLINT NOT NULL, 
          equipment_engine_capacity VARCHAR(255) NOT NULL, 
          equipment_transmission SMALLINT NOT NULL, 
          equipment_wheel_drive SMALLINT NOT NULL, 
          case_type SMALLINT NOT NULL, 
          mileage INT NOT NULL, 
          equipment_engine_name VARCHAR(255) DEFAULT NULL, 
          uuid UUID NOT NULL, 
          vehicle_id UUID DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_773de69d772e836a ON car (identifier)');
        $this->addSql('CREATE UNIQUE INDEX uniq_773de69dd17f50a6 ON car (uuid)');
        $this->addSql('CREATE INDEX idx_773de69d7e3c61f9 ON car (owner_id)');
        $this->addSql('COMMENT ON COLUMN car.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.case_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE operand (
          id SERIAL NOT NULL, 
          type VARCHAR(255) NOT NULL, 
          contractor BOOLEAN NOT NULL, 
          seller BOOLEAN NOT NULL, 
          email VARCHAR(255) DEFAULT NULL, 
          uuid UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_83e03ce6d17f50a6 ON operand (uuid)');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE TABLE operand_note (
          id SERIAL NOT NULL, 
          operand_id INT DEFAULT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_36bde44118d7f226 ON operand_note (operand_id)');
        $this->addSql('COMMENT ON COLUMN operand_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE organization (
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          address VARCHAR(255) DEFAULT NULL, 
          telephone VARCHAR(35) DEFAULT NULL, 
          office_phone VARCHAR(35) DEFAULT NULL, 
          requisite_ogrn VARCHAR(255) DEFAULT NULL, 
          requisite_inn VARCHAR(255) DEFAULT NULL, 
          requisite_kpp VARCHAR(255) DEFAULT NULL, 
          requisite_rs VARCHAR(255) DEFAULT NULL, 
          requisite_ks VARCHAR(255) DEFAULT NULL, 
          requisite_bik VARCHAR(255) DEFAULT NULL, 
          requisite_bank VARCHAR(255) DEFAULT NULL, 
          requisite_legal_address VARCHAR(255) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN organization.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN organization.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE person (
          id INT NOT NULL, 
          firstname VARCHAR(32) DEFAULT NULL, 
          lastname VARCHAR(255) DEFAULT NULL, 
          telephone VARCHAR(35) DEFAULT NULL, 
          office_phone VARCHAR(35) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_34dcd176450ff010 ON person (telephone)');
        $this->addSql('COMMENT ON COLUMN person.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN person.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE car_recommendation (
          id SERIAL NOT NULL, 
          car_id INT DEFAULT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          service VARCHAR(255) NOT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          realization_tenant SMALLINT DEFAULT NULL, 
          realization_id INT DEFAULT NULL, 
          uuid UUID NOT NULL, 
          created_by UUID NOT NULL, 
          worker_id UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_8e4baaf2c3c6f69f ON car_recommendation (car_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8e4baaf2d17f50a6 ON car_recommendation (uuid)');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization_tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('ALTER TABLE 
          car_note 
        ADD 
          CONSTRAINT fk_4d7eeb8c3c6f69f FOREIGN KEY (car_id) REFERENCES car (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation_part 
        ADD 
          CONSTRAINT fk_ddc72d65d173940b FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT fk_773de69d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES operand (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          operand_note 
        ADD 
          CONSTRAINT fk_36bde44118d7f226 FOREIGN KEY (operand_id) REFERENCES operand (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          organization 
        ADD 
          CONSTRAINT fk_c1ee637cbf396750 FOREIGN KEY (id) REFERENCES operand (id) ON UPDATE RESTRICT ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          person 
        ADD 
          CONSTRAINT fk_34dcd176bf396750 FOREIGN KEY (id) REFERENCES operand (id) ON UPDATE RESTRICT ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation 
        ADD 
          CONSTRAINT fk_3486230cc3c6f69f FOREIGN KEY (car_id) REFERENCES car (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE balance ADD operand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE 
          balance 
        ADD 
          CONSTRAINT fk_acf41ffe18d7f226 FOREIGN KEY (operand_id) REFERENCES operand (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_acf41ffe18d7f2264e59c462 ON balance (operand_id, tenant)');
        $this->addSql('CREATE INDEX idx_acf41ffe18d7f226 ON balance (operand_id)');
    }
}
