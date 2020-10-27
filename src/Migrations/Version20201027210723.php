<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201027210723 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE appeal_calculator (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(35) NOT NULL, body JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE appeal_cooperation (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(35) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE appeal_question (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, question VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE appeal_schedule (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE appeal_tire_fitting (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(35) NOT NULL, body JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.phone IS \'(DC2Type:phone_number)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appeal_calculator');
        $this->addSql('DROP TABLE appeal_cooperation');
        $this->addSql('DROP TABLE appeal_question');
        $this->addSql('DROP TABLE appeal_schedule');
        $this->addSql('DROP TABLE appeal_tire_fitting');
    }
}
