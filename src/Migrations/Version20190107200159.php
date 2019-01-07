<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107200159 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8FF28C0D8');
        $this->addSql('DROP TABLE partner_supply_import');
        $this->addSql('DROP TABLE supply');
        $this->addSql('DROP INDEX IDX_834566E8FF28C0D8 ON income_part');
        $this->addSql('ALTER TABLE income_part DROP supply_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE partner_supply_import (id INT AUTO_INCREMENT NOT NULL, external_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, date DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE supply (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, part_id INT NOT NULL, received_by_id INT DEFAULT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', received_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', price_amount VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, price_currency_code VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_D219948C2ADD6D8C (supplier_id), INDEX IDX_D219948C6F8DDD17 (received_by_id), INDEX IDX_D219948C4CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE income_part ADD supply_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8FF28C0D8 FOREIGN KEY (supply_id) REFERENCES supply (id)');
        $this->addSql('CREATE INDEX IDX_834566E8FF28C0D8 ON income_part (supply_id)');
    }
}
