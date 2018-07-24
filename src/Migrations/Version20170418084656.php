<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170418084656 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders CHANGE startdate created_at DATETIME NOT NULL, CHANGE closeddate closed_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE orders SET created_at = closed_at WHERE created_at IS NULL AND closed_at IS NOT NULL');
        $this->addSql('DELETE orders FROM orders WHERE created_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders ADD closeddate DATETIME DEFAULT NULL, DROP created_at, CHANGE closed_at startdate DATETIME DEFAULT NULL');
    }
}
