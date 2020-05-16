<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200513121711 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE car DROP CONSTRAINT fk_773de69d7e3c61f9');
        $this->addSql('DROP INDEX idx_773de69d7e3c61f9');
        $this->addSql('ALTER TABLE car DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE car ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT fk_773de69d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_773de69d7e3c61f9 ON car (owner_id)');
    }
}
