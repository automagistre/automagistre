<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410124137 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, operand_id INT DEFAULT NULL, tenant SMALLINT NOT NULL COMMENT \'(DC2Type:tenant_enum)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_ACF41FFE18D7F226 (operand_id), UNIQUE INDEX UNIQUE_IDX (operand_id, tenant), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFE18D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('DROP TABLE balance');
    }
}
