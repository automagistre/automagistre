<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200620152533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE order_salary_id_seq CASCADE');
        $this->addSql('DROP TABLE order_salary');

        $this->addSql('ALTER TABLE wallet_transaction RENAME TO wallet_transaction_old');

        $this->addSql('CREATE TABLE customer_transaction (id UUID NOT NULL, operand_id UUID NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, source SMALLINT NOT NULL, source_id UUID NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN customer_transaction.id IS \'(DC2Type:customer_transaction_id)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.operand_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.source IS \'(DC2Type:operand_transaction_source)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.source_id IS \'(DC2Type:uuid)\'');

        $this->addSql('CREATE TABLE wallet_transaction (id UUID NOT NULL, wallet_id UUID NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, source SMALLINT NOT NULL, source_id UUID NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.id IS \'(DC2Type:wallet_transaction_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.wallet_id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.source IS \'(DC2Type:wallet_transaction_source)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER INDEX idx_7daf972712520f3 RENAME TO IDX_ECA65561712520F3');

        $this->addSql('DROP TABLE expense_item');
        $this->addSql('DROP TABLE salary');
        $this->addSql('DROP TABLE IF EXISTS penalty');
        $this->addSql('DROP TABLE wallet_transaction_old');
        $this->addSql('DROP TABLE operand_transaction');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer_transaction');
    }
}
