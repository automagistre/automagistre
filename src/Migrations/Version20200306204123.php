<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200306204123 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE 
          wallet_transaction 
        DROP 
          subtotal_amount, 
        DROP 
          subtotal_currency_code, 
          CHANGE description description TEXT NOT NULL');
        $this->addSql('ALTER TABLE 
          operand_transaction 
        DROP 
          subtotal_amount, 
        DROP 
          subtotal_currency_code, 
          CHANGE description description TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE 
          operand_transaction 
        ADD 
          subtotal_amount VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
        ADD 
          subtotal_currency_code VARCHAR(3) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
          CHANGE description description TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE 
          wallet_transaction 
        ADD 
          subtotal_amount VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
        ADD 
          subtotal_currency_code VARCHAR(3) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, 
          CHANGE description description TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
