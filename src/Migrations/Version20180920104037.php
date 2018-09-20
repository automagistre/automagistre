<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180920104037 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment ADD amount_currency_code VARCHAR(3) DEFAULT NULL, CHANGE subtotal subtotal_amount VARCHAR(255) DEFAULT NULL, ADD subtotal_currency_code VARCHAR(3) DEFAULT NULL, CHANGE amount amount_amount VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE payment SET amount_currency_code = \'RUB\' WHERE amount_currency_code IS NULL');
        $this->addSql('UPDATE payment SET subtotal_currency_code = \'RUB\' WHERE subtotal_currency_code IS NULL');
        $this->addSql('UPDATE payment SET subtotal_amount = 0 WHERE subtotal_amount IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment ADD amount VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD subtotal VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP amount_amount, DROP amount_currency_code, DROP subtotal_amount, DROP subtotal_currency_code');
    }
}
