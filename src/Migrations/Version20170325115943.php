<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325115943 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX part_idx ON part');
        $this->addSql('ALTER TABLE part CHANGE partname name VARCHAR(255) DEFAULT NULL, CHANGE partnumber number VARCHAR(30) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX part_idx ON part (number, manufacturer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX part_idx ON part');
        $this->addSql('ALTER TABLE part CHANGE name partname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE number partnumber VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX part_idx ON part (partnumber, manufacturer_id)');
    }
}
