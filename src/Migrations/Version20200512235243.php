<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200512235243 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE car (
          id SERIAL NOT NULL, 
          identifier VARCHAR(17) DEFAULT NULL, 
          year INT DEFAULT NULL, 
          owner_id INT DEFAULT NULL, 
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON car (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A ON car (identifier)');
        $this->addSql('CREATE INDEX IDX_773DE69D7E3C61F9 ON car (owner_id)');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN car.case_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('CREATE TABLE car_note (
          id SERIAL NOT NULL, 
          car_id INT DEFAULT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4D7EEB8C3C6F69F ON car_note (car_id)');
        $this->addSql('COMMENT ON COLUMN car_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_at IS \'(DC2Type:datetime_immutable)\'');
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E4BAAF2D17F50A6 ON car_recommendation (uuid)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2C3C6F69F ON car_recommendation (car_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization_tenant IS \'(DC2Type:tenant_enum)\'');
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDC72D65D17F50A6 ON car_recommendation_part (uuid)');
        $this->addSql('CREATE INDEX IDX_DDC72D65D173940B ON car_recommendation_part (recommendation_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:recommendation_part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE operand (
          id SERIAL NOT NULL, 
          type VARCHAR(255) NOT NULL, 
          contractor BOOLEAN NOT NULL, 
          seller BOOLEAN NOT NULL, 
          email VARCHAR(255) DEFAULT NULL, 
          uuid UUID NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');
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
        $this->addSql('CREATE INDEX IDX_36BDE44118D7F226 ON operand_note (operand_id)');
        $this->addSql('COMMENT ON COLUMN operand_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.type IS \'(DC2Type:note_type_enum)\'');
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176450FF010 ON person (telephone)');
        $this->addSql('COMMENT ON COLUMN person.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN person.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT FK_773DE69D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_note 
        ADD 
          CONSTRAINT FK_4D7EEB8C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation 
        ADD 
          CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation_part 
        ADD 
          CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          operand_note 
        ADD 
          CONSTRAINT FK_36BDE44118D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          organization 
        ADD 
          CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          person 
        ADD 
          CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE car_note DROP CONSTRAINT FK_4D7EEB8C3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation DROP CONSTRAINT FK_8E4BAAF2C3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation_part DROP CONSTRAINT FK_DDC72D65D173940B');
        $this->addSql('ALTER TABLE car DROP CONSTRAINT FK_773DE69D7E3C61F9');
        $this->addSql('ALTER TABLE operand_note DROP CONSTRAINT FK_36BDE44118D7F226');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT FK_34DCD176BF396750');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE person');
    }
}
