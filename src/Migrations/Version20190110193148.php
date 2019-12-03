<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190110193148 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, wallet_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2D3A8DA6712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_item (id INT AUTO_INCREMENT NOT NULL, expense_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount_amount VARCHAR(255) DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', INDEX IDX_ABBC6B7CF395DB7B (expense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE expense_item ADD CONSTRAINT FK_ABBC6B7CF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE expense_item DROP FOREIGN KEY FK_ABBC6B7CF395DB7B');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_item');
    }
}
