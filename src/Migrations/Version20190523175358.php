<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190523175358 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('ALTER TABLE organization ADD requisite_legal_address VARCHAR(255) DEFAULT NULL, CHANGE requisite_bank requisite_bank VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('ALTER TABLE organization DROP requisite_legal_address, CHANGE requisite_bank requisite_bank VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
