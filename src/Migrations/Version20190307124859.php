<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190307124859 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('CREATE TABLE order_salary (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, INDEX IDX_579CABA48D9F6D38 (order_id), INDEX IDX_579CABA42FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_salary ADD CONSTRAINT FK_579CABA48D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_salary ADD CONSTRAINT FK_579CABA42FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id)');

        $this->addSql('CREATE TABLE order_contractor (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, contractor_id INT DEFAULT NULL, money_amount VARCHAR(255) DEFAULT NULL, money_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_F0A12FBA8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_contractor ADD CONSTRAINT FK_F0A12FBA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('DROP TABLE order_contractor');
        $this->addSql('DROP TABLE order_salary');
    }
}
