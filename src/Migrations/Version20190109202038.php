<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109202038 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('CREATE TABLE car_recommendation (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, worker_id INT NOT NULL, created_by_id INT NOT NULL, service VARCHAR(255) NOT NULL, expired_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', realization_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', realization_tenant INT NOT NULL, price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_8E4BAAF2C3C6F69F (car_id), INDEX IDX_8E4BAAF26B20BA36 (worker_id), INDEX IDX_8E4BAAF2B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_credentials (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, payloads LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_531EE19BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, tenant_id INT DEFAULT NULL, created_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, arguments LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BAE0AA79033212A (tenant_id), INDEX IDX_3BAE0AA7B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_work (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_line (id INT AUTO_INCREMENT NOT NULL, equipment_id INT DEFAULT NULL, work_id INT DEFAULT NULL, period INT NOT NULL, recommended TINYINT(1) NOT NULL, INDEX IDX_B37EBC5F517FE9FE (equipment_id), INDEX IDX_B37EBC5FBB3453DB (work_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_part (id INT AUTO_INCREMENT NOT NULL, line_id INT DEFAULT NULL, part_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, recommended TINYINT(1) NOT NULL, INDEX IDX_2B65786F4D7B7542 (line_id), INDEX IDX_2B65786F4CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_equipment (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, engine VARCHAR(255) NOT NULL, engine_capacity VARCHAR(255) NOT NULL, transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\', period INT NOT NULL, INDEX IDX_793047587975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operand (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, contractor TINYINT(1) NOT NULL, seller TINYINT(1) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', type INT NOT NULL, UNIQUE INDEX UNIQ_83E03CE6D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, telephone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', office_phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, number VARCHAR(30) NOT NULL, description TEXT DEFAULT NULL, negative TINYINT(1) DEFAULT NULL, fractional TINYINT(1) DEFAULT NULL, quantity DOUBLE PRECISION DEFAULT NULL, reserved INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, UNIQUE INDEX UNIQ_490F70C6D17F50A6 (uuid), INDEX IDX_490F70C6A23B42D (manufacturer_id), UNIQUE INDEX part_idx (number, manufacturer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_recommendation_part (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, part_id INT DEFAULT NULL, created_by_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_DDC72D65D173940B (recommendation_id), INDEX IDX_DDC72D654CE34BEC (part_id), INDEX IDX_DDC72D65B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_note (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, created_by_id INT NOT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4D7EEB8C3C6F69F (car_id), INDEX IDX_4D7EEB8B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, author VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, publish_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4E59C462772E836A (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9D17F50A6 (uuid), UNIQUE INDEX UNIQ_1483A5E9217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manufacturer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL, localized_name VARCHAR(255) DEFAULT NULL, logo VARCHAR(25) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', UNIQUE INDEX UNIQ_3D0AE6DCD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT NOT NULL, firstname VARCHAR(32) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, telephone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', office_phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', UNIQUE INDEX UNIQ_34DCD176450FF010 (telephone), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operand_note (id INT AUTO_INCREMENT NOT NULL, operand_id INT DEFAULT NULL, created_by_id INT NOT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_36BDE44118D7F226 (operand_id), INDEX IDX_36BDE441B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_model (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT NOT NULL, name VARCHAR(255) NOT NULL, localized_name VARCHAR(255) DEFAULT NULL, case_name VARCHAR(255) DEFAULT NULL, year_from SMALLINT DEFAULT NULL, year_till SMALLINT DEFAULT NULL, INDEX IDX_83EF70EA23B42D (manufacturer_id), UNIQUE INDEX MANUFACTURER_CASE_IDX (manufacturer_id, case_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_cross (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_cross_part (part_cross_id INT NOT NULL, part_id INT NOT NULL, INDEX IDX_B98F499C70B9088C (part_cross_id), UNIQUE INDEX UNIQ_B98F499C4CE34BEC (part_id), PRIMARY KEY(part_cross_id, part_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, car_model_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type)\', engine_capacity VARCHAR(255) DEFAULT NULL, transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\', vin VARCHAR(17) DEFAULT NULL, year INT DEFAULT NULL, case_type SMALLINT NOT NULL COMMENT \'(DC2Type:carcase)\', gosnomer VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, mileage INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_773DE69DB1085141 (vin), UNIQUE INDEX UNIQ_773DE69DD17F50A6 (uuid), INDEX IDX_773DE69DF64382E3 (car_model_id), INDEX IDX_773DE69D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF26B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_credentials ADD CONSTRAINT FK_531EE19BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id)');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id)');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id)');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT FK_2B65786F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE mc_equipment ADD CONSTRAINT FK_793047587975B7E7 FOREIGN KEY (model_id) REFERENCES car_model (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id)');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D654CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT FK_4D7EEB8C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT FK_4D7EEB8B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT FK_36BDE44118D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT FK_36BDE441B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE car_model ADD CONSTRAINT FK_83EF70EA23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DF64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES operand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65D173940B');
        $this->addSql('ALTER TABLE mc_line DROP FOREIGN KEY FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE mc_part DROP FOREIGN KEY FK_2B65786F4D7B7542');
        $this->addSql('ALTER TABLE mc_line DROP FOREIGN KEY FK_B37EBC5F517FE9FE');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF26B20BA36');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176BF396750');
        $this->addSql('ALTER TABLE operand_note DROP FOREIGN KEY FK_36BDE44118D7F226');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D7E3C61F9');
        $this->addSql('ALTER TABLE mc_part DROP FOREIGN KEY FK_2B65786F4CE34BEC');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D654CE34BEC');
        $this->addSql('ALTER TABLE part_cross_part DROP FOREIGN KEY FK_B98F499C4CE34BEC');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA79033212A');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF2B03A8386');
        $this->addSql('ALTER TABLE user_credentials DROP FOREIGN KEY FK_531EE19BA76ED395');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7B03A8386');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65B03A8386');
        $this->addSql('ALTER TABLE car_note DROP FOREIGN KEY FK_4D7EEB8B03A8386');
        $this->addSql('ALTER TABLE operand_note DROP FOREIGN KEY FK_36BDE441B03A8386');
        $this->addSql('ALTER TABLE part DROP FOREIGN KEY FK_490F70C6A23B42D');
        $this->addSql('ALTER TABLE car_model DROP FOREIGN KEY FK_83EF70EA23B42D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9217BBB47');
        $this->addSql('ALTER TABLE mc_equipment DROP FOREIGN KEY FK_793047587975B7E7');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DF64382E3');
        $this->addSql('ALTER TABLE part_cross_part DROP FOREIGN KEY FK_B98F499C70B9088C');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF2C3C6F69F');
        $this->addSql('ALTER TABLE car_note DROP FOREIGN KEY FK_4D7EEB8C3C6F69F');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE user_credentials');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_part');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE car');
    }
}
