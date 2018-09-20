<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180916190042 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operand ADD email VARCHAR(255) DEFAULT NULL');

        $this->addSql('UPDATE operand JOIN organization ON operand.id = organization.id SET operand.email = organization.email');
        $this->addSql('UPDATE operand JOIN person on operand.id = person.id SET operand.email = person.email');

        $this->addSql('ALTER TABLE person DROP email');
        $this->addSql('ALTER TABLE organization DROP email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operand DROP email');
        $this->addSql('ALTER TABLE organization ADD email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE person ADD email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
