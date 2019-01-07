<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107133119 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wallet_transaction (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description TEXT DEFAULT NULL, amount_amount VARCHAR(255) DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, subtotal_amount VARCHAR(255) DEFAULT NULL, subtotal_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_7DAF972E92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, use_in_income TINYINT(1) NOT NULL, use_in_order TINYINT(1) NOT NULL, currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wallet ADD show_in_layout TINYINT(1) NOT NULL');
        $this->addSql('RENAME TABLE payment TO operand_transaction');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF972E92F8F78 FOREIGN KEY (recipient_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE operand_transaction ADD CONSTRAINT FK_C168F907E92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');

        $this->addSql('INSERT INTO wallet (id, name, use_in_income, use_in_order, show_in_layout, currency_code) VALUES (1, \'Касса\', 1, 1, 1, \'RUB\'), (2, \'Безнал\', 1, 1, 0, \'RUB\')');
        $this->addSql('INSERT INTO wallet_transaction (recipient_id, created_at, description, amount_amount, amount_currency_code, subtotal_amount, subtotal_currency_code)
        SELECT 1, created_at, description, amount_amount, amount_currency_code, subtotal_amount, subtotal_currency_code FROM operand_transaction WHERE recipient_id = 1');
        $this->addSql('INSERT INTO wallet_transaction (recipient_id, created_at, description, amount_amount, amount_currency_code, subtotal_amount, subtotal_currency_code)
        SELECT 2, created_at, description, amount_amount, amount_currency_code, subtotal_amount, subtotal_currency_code FROM operand_transaction WHERE recipient_id = 2422');
        $this->addSql('DELETE FROM operand_transaction WHERE recipient_id IN (1, 2422)');

        $this->addSql('ALTER TABLE operand_transaction DROP FOREIGN KEY FK_C168F907E92F8F78');
        $this->addSql('ALTER TABLE operand_transaction DROP FOREIGN KEY FK_6D28840DE92F8F78');
        $this->addSql('DROP INDEX idx_6d28840de92f8f78 ON operand_transaction');
        $this->addSql('CREATE INDEX IDX_C168F907E92F8F78 ON operand_transaction (recipient_id)');
        $this->addSql('ALTER TABLE operand_transaction ADD CONSTRAINT FK_6D28840DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_c168f907e92f8f78 ON operand_transaction');
        $this->addSql('CREATE INDEX IDX_6D28840DE92F8F78 ON operand_transaction (recipient_id)');
        $this->addSql('ALTER TABLE operand_transaction ADD CONSTRAINT FK_C168F907E92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');

        $this->addSql('ALTER TABLE wallet_transaction DROP FOREIGN KEY FK_7DAF972E92F8F78');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, description TEXT DEFAULT NULL COLLATE utf8_unicode_ci, amount_amount VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, subtotal_amount VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount_currency_code VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, subtotal_currency_code VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_6D28840DE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');
        $this->addSql('DROP TABLE wallet_transaction');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE operand_transaction');
    }
}
