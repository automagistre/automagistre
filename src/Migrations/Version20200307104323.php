<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200307104323 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('CREATE SEQUENCE part_case_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE balance_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE stockpile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE part_cross_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_work_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_line_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mc_equipment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_model_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_recommendation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE car_recommendation_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_credentials_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE part_case (
          id INT NOT NULL, 
          part_id INT DEFAULT NULL, 
          car_model_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2A0E7894CE34BEC ON part_case (part_id)');
        $this->addSql('CREATE INDEX IDX_2A0E789F64382E3 ON part_case (car_model_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BECF64382E3 ON part_case (part_id, car_model_id)');
        $this->addSql('CREATE TABLE balance (
          id INT NOT NULL, 
          operand_id INT DEFAULT NULL, 
          tenant SMALLINT NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_ACF41FFE18D7F226 ON balance (operand_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ACF41FFE18D7F2264E59C462 ON balance (operand_id, tenant)');
        $this->addSql('COMMENT ON COLUMN balance.tenant IS \'(DC2Type:tenant_enum)\'');
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
        $this->addSql('CREATE TABLE operand (
          id INT NOT NULL, 
          email VARCHAR(255) DEFAULT NULL, 
          contractor BOOLEAN NOT NULL, 
          seller BOOLEAN NOT NULL, 
          type INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE organization (
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          address VARCHAR(255) DEFAULT NULL, 
          telephone VARCHAR(35) DEFAULT NULL, 
          office_phone VARCHAR(35) DEFAULT NULL, 
          requisite_bank VARCHAR(255) DEFAULT NULL, 
          requisite_legal_address VARCHAR(255) DEFAULT NULL, 
          requisite_ogrn VARCHAR(255) DEFAULT NULL, 
          requisite_inn VARCHAR(255) DEFAULT NULL, 
          requisite_kpp VARCHAR(255) DEFAULT NULL, 
          requisite_rs VARCHAR(255) DEFAULT NULL, 
          requisite_ks VARCHAR(255) DEFAULT NULL, 
          requisite_bik VARCHAR(255) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN organization.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN organization.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE stockpile (
          id INT NOT NULL, 
          part_id INT DEFAULT NULL, 
          tenant SMALLINT NOT NULL, 
          quantity INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C2E8923F4CE34BEC ON stockpile (part_id)');
        $this->addSql('CREATE INDEX IDX_C2E8923F4CE34BEC4E59C4629FF31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2E8923F4CE34BEC4E59C462 ON stockpile (part_id, tenant)');
        $this->addSql('COMMENT ON COLUMN stockpile.tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE operand_note (
          id INT NOT NULL, 
          operand_id INT DEFAULT NULL, 
          created_by_id INT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_36BDE44118D7F226 ON operand_note (operand_id)');
        $this->addSql('CREATE INDEX IDX_36BDE441B03A8386 ON operand_note (created_by_id)');
        $this->addSql('COMMENT ON COLUMN operand_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('CREATE TABLE part (
          id INT NOT NULL, 
          manufacturer_id INT DEFAULT NULL, 
          name VARCHAR(255) NOT NULL, 
          number VARCHAR(30) NOT NULL, 
          universal BOOLEAN NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          discount_amount VARCHAR(255) DEFAULT NULL, 
          discount_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_490F70C6A23B42D ON part (manufacturer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D ON part (number, manufacturer_id)');
        $this->addSql('CREATE TABLE part_part (
          part_source INT NOT NULL, 
          part_target INT NOT NULL, 
          PRIMARY KEY(part_source, part_target)
        )');
        $this->addSql('CREATE INDEX IDX_33A70E4B661ABFE6 ON part_part (part_source)');
        $this->addSql('CREATE INDEX IDX_33A70E4B7FFFEF69 ON part_part (part_target)');
        $this->addSql('CREATE TABLE event (
          id INT NOT NULL, 
          created_by_id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          arguments JSON NOT NULL, 
          tenant SMALLINT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7B03A8386 ON event (created_by_id)');
        $this->addSql('COMMENT ON COLUMN event.arguments IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN event.tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE part_cross (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE part_cross_part (
          part_cross_id INT NOT NULL, 
          part_id INT NOT NULL, 
          PRIMARY KEY(part_cross_id, part_id)
        )');
        $this->addSql('CREATE INDEX IDX_B98F499C70B9088C ON part_cross_part (part_cross_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B98F499C4CE34BEC ON part_cross_part (part_id)');
        $this->addSql('CREATE TABLE mc_work (
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          description VARCHAR(255) DEFAULT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE mc_part (
          id INT NOT NULL, 
          line_id INT DEFAULT NULL, 
          part_id INT DEFAULT NULL, 
          quantity INT NOT NULL, 
          recommended BOOLEAN NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2B65786F4D7B7542 ON mc_part (line_id)');
        $this->addSql('CREATE INDEX IDX_2B65786F4CE34BEC ON mc_part (part_id)');
        $this->addSql('CREATE TABLE mc_line (
          id INT NOT NULL, 
          equipment_id INT DEFAULT NULL, 
          work_id INT DEFAULT NULL, 
          period INT NOT NULL, 
          recommended BOOLEAN NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B37EBC5F517FE9FE ON mc_line (equipment_id)');
        $this->addSql('CREATE INDEX IDX_B37EBC5FBB3453DB ON mc_line (work_id)');
        $this->addSql('CREATE TABLE mc_equipment (
          id INT NOT NULL, 
          model_id INT DEFAULT NULL, 
          period INT NOT NULL, 
          equipment_transmission SMALLINT NOT NULL, 
          equipment_wheel_drive SMALLINT NOT NULL, 
          equipment_engine_name VARCHAR(255) DEFAULT NULL, 
          equipment_engine_type SMALLINT NOT NULL, 
          equipment_engine_capacity VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_793047587975B7E7 ON mc_equipment (model_id)');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('CREATE TABLE review (
          id INT NOT NULL, 
          author VARCHAR(255) NOT NULL, 
          manufacturer VARCHAR(255) NOT NULL, 
          model VARCHAR(255) NOT NULL, 
          content TEXT NOT NULL, 
          url VARCHAR(255) NOT NULL, 
          publish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN review.publish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE car_model (
          id INT NOT NULL, 
          manufacturer_id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          localized_name VARCHAR(255) DEFAULT NULL, 
          case_name VARCHAR(255) DEFAULT NULL, 
          year_from SMALLINT DEFAULT NULL, 
          year_till SMALLINT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_83EF70EA23B42D ON car_model (manufacturer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83EF70EA23B42DDF3BA4B5 ON car_model (manufacturer_id, case_name)');
        $this->addSql('CREATE TABLE car (
          id INT NOT NULL, 
          model_id INT DEFAULT NULL, 
          owner_id INT DEFAULT NULL, 
          identifier VARCHAR(17) DEFAULT NULL, 
          year INT DEFAULT NULL, 
          case_type SMALLINT NOT NULL, 
          description TEXT DEFAULT NULL, 
          gosnomer VARCHAR(255) DEFAULT NULL, 
          mileage INT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          equipment_transmission SMALLINT NOT NULL, 
          equipment_wheel_drive SMALLINT NOT NULL, 
          equipment_engine_name VARCHAR(255) DEFAULT NULL, 
          equipment_engine_type SMALLINT NOT NULL, 
          equipment_engine_capacity VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A ON car (identifier)');
        $this->addSql('CREATE INDEX IDX_773DE69D7975B7E7 ON car (model_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D7E3C61F9 ON car (owner_id)');
        $this->addSql('COMMENT ON COLUMN car.case_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('CREATE TABLE car_note (
          id INT NOT NULL, 
          car_id INT DEFAULT NULL, 
          created_by_id INT NOT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4D7EEB8C3C6F69F ON car_note (car_id)');
        $this->addSql('CREATE INDEX IDX_4D7EEB8B03A8386 ON car_note (created_by_id)');
        $this->addSql('COMMENT ON COLUMN car_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE car_recommendation (
          id INT NOT NULL, 
          car_id INT DEFAULT NULL, 
          worker_id INT NOT NULL, 
          created_by_id INT NOT NULL, 
          service VARCHAR(255) NOT NULL, 
          expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          realization_id INT DEFAULT NULL, 
          realization_tenant SMALLINT DEFAULT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2C3C6F69F ON car_recommendation (car_id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF26B20BA36 ON car_recommendation (worker_id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2B03A8386 ON car_recommendation (created_by_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization_tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE car_recommendation_part (
          id INT NOT NULL, 
          recommendation_id INT DEFAULT NULL, 
          part_id INT DEFAULT NULL, 
          created_by_id INT NOT NULL, 
          quantity INT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_DDC72D65D173940B ON car_recommendation_part (recommendation_id)');
        $this->addSql('CREATE INDEX IDX_DDC72D654CE34BEC ON car_recommendation_part (part_id)');
        $this->addSql('CREATE INDEX IDX_DDC72D65B03A8386 ON car_recommendation_part (created_by_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE manufacturer (
          id INT NOT NULL, 
          name VARCHAR(64) DEFAULT NULL, 
          localized_name VARCHAR(255) DEFAULT NULL, 
          logo VARCHAR(25) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE users (
          id INT NOT NULL, 
          person_id INT DEFAULT NULL, 
          roles TEXT NOT NULL, 
          username VARCHAR(255) NOT NULL, 
          tenants JSON NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9217BBB47 ON users (person_id)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN users.tenants IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE user_credentials (
          id INT NOT NULL, 
          user_id INT DEFAULT NULL, 
          type VARCHAR(255) NOT NULL, 
          identifier VARCHAR(255) NOT NULL, 
          payloads TEXT NOT NULL, 
          expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_531EE19BA76ED395 ON user_credentials (user_id)');
        $this->addSql('COMMENT ON COLUMN user_credentials.payloads IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE 
          part_case 
        ADD 
          CONSTRAINT FK_2A0E7894CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_case 
        ADD 
          CONSTRAINT FK_2A0E789F64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          balance 
        ADD 
          CONSTRAINT FK_ACF41FFE18D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          person 
        ADD 
          CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          organization 
        ADD 
          CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          stockpile 
        ADD 
          CONSTRAINT FK_C2E8923F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          operand_note 
        ADD 
          CONSTRAINT FK_36BDE44118D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          operand_note 
        ADD 
          CONSTRAINT FK_36BDE441B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part 
        ADD 
          CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_part 
        ADD 
          CONSTRAINT FK_33A70E4B661ABFE6 FOREIGN KEY (part_source) REFERENCES part (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_part 
        ADD 
          CONSTRAINT FK_33A70E4B7FFFEF69 FOREIGN KEY (part_target) REFERENCES part (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          event 
        ADD 
          CONSTRAINT FK_3BAE0AA7B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_cross_part 
        ADD 
          CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_cross_part 
        ADD 
          CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          mc_part 
        ADD 
          CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          mc_part 
        ADD 
          CONSTRAINT FK_2B65786F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          mc_line 
        ADD 
          CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          mc_line 
        ADD 
          CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          mc_equipment 
        ADD 
          CONSTRAINT FK_793047587975B7E7 FOREIGN KEY (model_id) REFERENCES car_model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_model 
        ADD 
          CONSTRAINT FK_83EF70EA23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT FK_773DE69D7975B7E7 FOREIGN KEY (model_id) REFERENCES car_model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT FK_773DE69D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_note 
        ADD 
          CONSTRAINT FK_4D7EEB8C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_note 
        ADD 
          CONSTRAINT FK_4D7EEB8B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation 
        ADD 
          CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation 
        ADD 
          CONSTRAINT FK_8E4BAAF26B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation 
        ADD 
          CONSTRAINT FK_8E4BAAF2B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation_part 
        ADD 
          CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation_part 
        ADD 
          CONSTRAINT FK_DDC72D654CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          car_recommendation_part 
        ADD 
          CONSTRAINT FK_DDC72D65B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          users 
        ADD 
          CONSTRAINT FK_1483A5E9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          user_credentials 
        ADD 
          CONSTRAINT FK_531EE19BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
    }
}
