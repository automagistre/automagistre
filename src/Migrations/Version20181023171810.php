<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181023171810 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mc_work (id INT AUTO_INCREMENT NOT NULL, equipment_id INT DEFAULT NULL, part_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, period INT NOT NULL, recommended TINYINT(1) NOT NULL, INDEX IDX_31246029517FE9FE (equipment_id), INDEX IDX_312460294CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_line (id INT AUTO_INCREMENT NOT NULL, work_id INT DEFAULT NULL, part_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, period INT NOT NULL, recommended TINYINT(1) NOT NULL, INDEX IDX_B37EBC5FBB3453DB (work_id), INDEX IDX_B37EBC5F4CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mc_equipment (id INT AUTO_INCREMENT NOT NULL, model_id INT DEFAULT NULL, engine VARCHAR(255) NOT NULL, engine_capacity VARCHAR(255) NOT NULL, transmission SMALLINT NOT NULL COMMENT \'(DC2Type:car_transmission)\', wheel_drive SMALLINT NOT NULL COMMENT \'(DC2Type:car_wheel_drive)\', period INT NOT NULL, INDEX IDX_793047587975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mc_work ADD CONSTRAINT FK_31246029517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id)');
        $this->addSql('ALTER TABLE mc_work ADD CONSTRAINT FK_312460294CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id)');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE mc_equipment ADD CONSTRAINT FK_793047587975B7E7 FOREIGN KEY (model_id) REFERENCES car_model (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mc_line DROP FOREIGN KEY FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE mc_work DROP FOREIGN KEY FK_31246029517FE9FE');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_equipment');
    }
}
