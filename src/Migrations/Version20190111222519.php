<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111222519 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('CREATE TABLE stockpile (id INT AUTO_INCREMENT NOT NULL, part_id INT DEFAULT NULL, tenant SMALLINT NOT NULL COMMENT \'(DC2Type:tenant_enum)\', quantity INT NOT NULL, INDEX IDX_C2E8923F4CE34BEC (part_id), INDEX SEARCH_IDX (part_id, tenant, quantity), UNIQUE INDEX PART_TENANT_UNIQUE_IDX (part_id, tenant), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stockpile ADD CONSTRAINT FK_C2E8923F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE stockpile');
    }
}
