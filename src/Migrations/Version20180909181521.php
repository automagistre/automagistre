<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180909181521 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_modification DROP FOREIGN KEY FK_B6BD9A3A56B8B385');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D71C30861');
        $this->addSql('DROP TABLE car_generation');
        $this->addSql('DROP TABLE car_modification');
        $this->addSql('DROP INDEX IDX_773DE69D71C30861 ON car');
        $this->addSql('ALTER TABLE car ADD engine_type SMALLINT NOT NULL COMMENT \'(DC2Type:engine_type)\', ADD engine_capacity INT DEFAULT NULL, ADD transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', ADD wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\', DROP car_modification_id, DROP sprite_id');
        $this->addSql('ALTER TABLE car_model ADD localized_name VARCHAR(255) DEFAULT NULL, ADD case_name VARCHAR(255) DEFAULT NULL, ADD case_type SMALLINT NOT NULL COMMENT \'(DC2Type:carcase)\', ADD year_from SMALLINT DEFAULT NULL, ADD year_till SMALLINT DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE manufacturer ADD localized_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE car_generation (id INT AUTO_INCREMENT NOT NULL, car_model_id INT NOT NULL, name VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_E1F9E22AF64382E3 (car_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_modification (id INT AUTO_INCREMENT NOT NULL, car_generation_id INT NOT NULL, name VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, hp SMALLINT DEFAULT NULL, doors SMALLINT DEFAULT NULL, `from` SMALLINT DEFAULT NULL, till SMALLINT DEFAULT NULL, maxspeed VARCHAR(20) DEFAULT NULL COLLATE utf8_unicode_ci, s0to100 VARCHAR(20) DEFAULT NULL COLLATE utf8_unicode_ci, tank SMALLINT DEFAULT NULL, `case` SMALLINT DEFAULT NULL COMMENT \'(DC2Type:carcase)\', engine VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, transmission SMALLINT DEFAULT NULL COMMENT \'(DC2Type:car_transmission)\', wheel_drive SMALLINT DEFAULT NULL COMMENT \'(DC2Type:car_wheel_drive)\', INDEX IDX_B6BD9A3AFBB2DD31 (car_generation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_generation ADD CONSTRAINT FK_E1F9E22A5E96AD46 FOREIGN KEY (car_model_id) REFERENCES car_model (id)');
        $this->addSql('ALTER TABLE car_modification ADD CONSTRAINT FK_B6BD9A3A56B8B385 FOREIGN KEY (car_generation_id) REFERENCES car_generation (id)');
        $this->addSql('ALTER TABLE car ADD sprite_id INT DEFAULT NULL, DROP engine_type, DROP transmission, DROP wheel_drive, CHANGE engine_capacity car_modification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D71C30861 FOREIGN KEY (car_modification_id) REFERENCES car_modification (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D71C30861 ON car (car_modification_id)');
        $this->addSql('ALTER TABLE car_model DROP localized_name, DROP case_name, DROP case_type, DROP year_from, DROP year_till, CHANGE name name VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE manufacturer DROP localized_name');
    }
}
