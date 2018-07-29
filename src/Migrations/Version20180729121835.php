<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180729121835 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person CHANGE telephone telephone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', CHANGE office_phone office_phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\'');
        $this->addSql('ALTER TABLE organization CHANGE telephone telephone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', CHANGE office_phone office_phone VARCHAR(35) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\'');

        $this->addSql('UPDATE person SET telephone = concat(\'+7\', telephone) WHERE telephone IS NOT NULL');
        $this->addSql('UPDATE person SET office_phone = concat(\'+7\', office_phone) WHERE office_phone IS NOT NULL');
        $this->addSql('UPDATE organization SET telephone = concat(\'+7\', telephone) WHERE telephone IS NOT NULL');
        $this->addSql('UPDATE organization SET office_phone = concat(\'+7\', office_phone) WHERE office_phone IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization CHANGE telephone telephone VARCHAR(24) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE person CHANGE telephone telephone VARCHAR(24) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(24) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
