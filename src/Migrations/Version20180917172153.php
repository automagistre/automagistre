<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180917172153 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_suspend (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, till DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reason VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C789F0D18D9F6D38 (order_id), INDEX IDX_C789F0D1B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_suspend ADD CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_suspend ADD CONSTRAINT FK_C789F0D1B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders DROP checkpay, DROP topay, DROP suspenddate, DROP suspended, DROP resumedate, DROP paycardbool, DROP paycard');
        $this->addSql('ALTER TABLE person DROP sprite_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE order_suspend');
        $this->addSql('ALTER TABLE orders ADD checkpay TINYINT(1) DEFAULT NULL, ADD topay DOUBLE PRECISION DEFAULT NULL, ADD suspenddate DATETIME DEFAULT NULL, ADD suspended TINYINT(1) DEFAULT NULL, ADD resumedate DATE DEFAULT NULL, ADD paycardbool TINYINT(1) DEFAULT NULL, ADD paycard INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD sprite_id INT DEFAULT NULL');
    }
}
