<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170402172900 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE motion CHANGE qty quantity INT NOT NULL');
        $this->addSql('UPDATE motion SET description = NULL WHERE LENGTH(description) = 0');
        $this->addSql('DELETE motion FROM motion WHERE reserve IS NOT NULL');
        $this->addSql('ALTER TABLE motion DROP reserve');
        $this->addSql('ALTER TABLE motion ADD order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion ADD CONSTRAINT FK_F5FEA1E88D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_F5FEA1E88D9F6D38 ON motion (order_id)');
        $this->addSql('
          UPDATE motion m
          LEFT JOIN orders ON orders.id = SUBSTR(m.description, 8)
            SET m.order_id = orders.id
          WHERE m.description LIKE \'Заказ #%\'
        ');
        $this->addSql('
            UPDATE motion m
              LEFT JOIN orders ON orders.id = SUBSTR(m.description, 8)
                SET m.order_id = orders.id
            WHERE m.description LIKE \'Order #%\'
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE motion ADD reserve INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion DROP FOREIGN KEY FK_F5FEA1E88D9F6D38');
        $this->addSql('DROP INDEX IDX_F5FEA1E88D9F6D38 ON motion');
        $this->addSql('ALTER TABLE motion CHANGE order_id reserve INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion CHANGE quantity qty INT DEFAULT NULL');
    }
}
