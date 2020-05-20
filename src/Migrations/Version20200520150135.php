<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200520150135 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part ALTER number TYPE VARCHAR(30)');
        $this->addSql('ALTER TABLE part ALTER number DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN part.number IS \'(DC2Type:part_number)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part ALTER number TYPE VARCHAR(30)');
        $this->addSql('ALTER TABLE part ALTER number DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN part.number IS NULL');
    }
}
