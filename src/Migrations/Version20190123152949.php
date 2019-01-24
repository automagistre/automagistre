<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190123152949 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE income ADD accrued_amount_amount VARCHAR(255) DEFAULT NULL, ADD accrued_amount_currency_code VARCHAR(3) DEFAULT NULL');

        $this->addSql('UPDATE income SET income.accrued_amount_amount = (SELECT SUM(income_part.price_amount * (income_part.quantity / 100)) FROM income_part WHERE income_part.income_id = income.id), income.accrued_amount_currency_code = \'RUB\' WHERE income.accrued_at IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE income DROP accrued_amount_amount, DROP accrued_amount_currency_code');
    }
}
