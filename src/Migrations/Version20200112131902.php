<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200112131902 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE orders ADD worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6B20BA36 ON orders (worker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE6B20BA36');
        $this->addSql('DROP INDEX IDX_E52FFDEE6B20BA36 ON orders');
        $this->addSql('ALTER TABLE orders DROP worker_id');
    }
}
