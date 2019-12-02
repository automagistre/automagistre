<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202173927 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('DROP INDEX UNIQ_773DE69DB1085141 ON car');
        $this->addSql('ALTER TABLE car CHANGE vin identifier VARCHAR(17) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A ON car (identifier)');

        $this->addSql('UPDATE car c SET c.identifier = TRIM(SUBSTRING_INDEX(description, \'VIN: \', -1)) WHERE c.id IN (SELECT c2.id FROM car c2 WHERE c2.description LIKE \'%VIN:%\')');
        $this->addSql('UPDATE car c SET description = NULL WHERE c.id IN (SELECT c2.id FROM car c2 WHERE c2.description LIKE \'%VIN:%\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_773DE69D772E836A ON car');
        $this->addSql('ALTER TABLE car CHANGE identifier vin VARCHAR(17) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DB1085141 ON car (vin)');
    }
}
