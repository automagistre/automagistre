<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111150944 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE TABLE salary (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_9413BB712FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE penalty (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_AFE28FD82FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB712FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id)');
        $this->addSql('ALTER TABLE penalty ADD CONSTRAINT FK_AFE28FD82FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE salary');
        $this->addSql('DROP TABLE penalty');
    }
}
