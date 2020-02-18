<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218204935 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('DROP TABLE order_report');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE TABLE order_report (
          id INT AUTO_INCREMENT NOT NULL, 
          order_id INT DEFAULT NULL, 
          path VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
          filename VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
          INDEX IDX_7A067518D9F6D38 (order_id), 
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'');
        $this->addSql('ALTER TABLE 
          order_report 
        ADD 
          CONSTRAINT FK_7A067518D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
    }
}
