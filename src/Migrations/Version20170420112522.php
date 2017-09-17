<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170420112522 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE supply');
        $this->addSql('CREATE TABLE supply (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, part_id INT NOT NULL, created_by_id INT DEFAULT NULL, received_by_id INT DEFAULT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, received_at DATETIME DEFAULT NULL, INDEX IDX_D219948C2ADD6D8C (supplier_id), INDEX IDX_D219948C4CE34BEC (part_id), INDEX IDX_D219948CB03A8386 (created_by_id), INDEX IDX_D219948C6F8DDD17 (received_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948CB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE supply ADD CONSTRAINT FK_D219948C6F8DDD17 FOREIGN KEY (received_by_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE supply');
    }
}
