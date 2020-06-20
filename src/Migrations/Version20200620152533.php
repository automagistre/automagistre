<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Balance\Entity\BalanceView;
use App\Customer\Entity\CustomerTransactionView;
use App\Customer\Enum\CustomerTransactionSource as CustomerSource;
use App\Wallet\Entity\WalletTransactionView;
use App\Wallet\Enum\WalletTransactionSource as WalletSource;
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

        // data migration
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at)
            SELECT ot.uuid, u.uuid, ot.created_at
            FROM operand_transaction ot
                     JOIN users u on u.id = ot.created_by_id
        ');
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at)
            SELECT wt.uuid, u.uuid, wt.created_at
            FROM wallet_transaction_old wt
                     JOIN users u on u.id = wt.created_by_id
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::operandManual()->toId().', ot.uuid, null
            FROM operand_transaction ot
                     JOIN wallet_transaction_old wt ON wt.created_at = ot.created_at
                AND ot.amount_amount = wt.amount_amount
                AND wt.description LIKE \'%\' || ot.description
            WHERE ot.description NOT LIKE \'#%\'
        ');
        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, ot.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::manual()->toId().', u.uuid, ot.description
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN wallet_transaction_old wt ON wt.created_at = ot.created_at
                AND ot.amount_amount = wt.amount_amount
                AND wt.description LIKE \'%\' || ot.description
                     JOIN users u ON u.id = ot.created_by_id
            WHERE ot.description NOT LIKE \'#%\'
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::operandManual()->toId().', ot.uuid, null
            FROM wallet_transaction_old wt
                    JOIN operand_transaction ot on ot.id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description LIKE \'# Ручная транзакция %\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT t.uuid, operand.uuid, t.amount_amount, t.amount_currency_code, '.CustomerSource::orderDebit()->toId().', o.uuid, null
            FROM operand_transaction t
                    JOIN orders o ON o.id = substring(t.description FROM \'[0-9]+\')::integer
                    JOIN operand ON operand.id = t.recipient_id
            WHERE t.description LIKE \'# Начисление по заказу #%\'        
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::orderDebit()->toId().', orders.uuid, null
            FROM wallet_transaction_old wt
                     JOIN orders ON orders.id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description LIKE \'# Начисление по заказу%\'
        ');
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::orderDebit()->toId().', orders.uuid, null
            FROM wallet_transaction_old wt
                    JOIN orders on orders.id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description LIKE \'%# Платеж по заказу #%\'
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT t.uuid, operand.uuid, t.amount_amount, t.amount_currency_code, '.CustomerSource::orderPayment()->toId().', o.uuid, null
            FROM operand_transaction t
                    JOIN orders o ON o.id = substring(t.description FROM \'[0-9]+\')::integer
                    JOIN operand ON operand.id = t.recipient_id
            WHERE t.description LIKE \'# Списание по заказу #%\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT t.uuid, operand.uuid, t.amount_amount, t.amount_currency_code, '.CustomerSource::orderSalary()->toId().', o.uuid, null
            FROM operand_transaction t
                    JOIN orders o ON o.id = substring(t.description FROM \'[0-9]+\')::integer
                    JOIN operand ON operand.id = t.recipient_id
            WHERE t.description LIKE \'# ЗП%\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::payroll()->toId().', wt.uuid, salary.description 
            FROM salary
                    JOIN operand_transaction ot on salary.income_id = ot.id
                    JOIN operand o on ot.recipient_id = o.id
                    JOIN users u ON ot.created_by_id = u.id
                    JOIN wallet_transaction_old wt on salary.outcome_id = wt.id
        ');
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::payroll()->toId().', ot.uuid, salary.description 
            FROM salary
                    JOIN operand_transaction ot on salary.income_id = ot.id
                    JOIN operand o on o.id = ot.recipient_id
                    JOIN wallet_transaction_old wt on salary.outcome_id = wt.id
        ');

        $this->addSql('ALTER TABLE salary DROP CONSTRAINT fk_9413bb71e6ee6d63');
        $this->addSql('ALTER TABLE salary DROP CONSTRAINT fk_9413bb71640ed2c0');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::incomeDebit()->toId().', income.id, null
            FROM operand_transaction ot
                    JOIN operand o on ot.recipient_id = o.id
                    JOIN income ON substr(ot.description, 27)::uuid = income.id
            WHERE ot.description LIKE \'# Начисление по поставке №%\'
                    AND ot.description LIKE \'%-%\'
        ');
        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::incomeDebit()->toId().', income.id, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN income ON income.old_id = substr(ot.description, 27)::integer
            WHERE ot.description LIKE \'# Начисление по поставке №%\'
                    AND ot.description NOT LIKE \'%-%\'
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::incomePayment()->toId().', income.id, null
            FROM operand_transaction ot
                    JOIN operand o on o.id = ot.recipient_id
                    JOIN income ON income.id = substr(ot.description, 23)::uuid
            WHERE ot.description LIKE \'# Оплата за поставку #%\'
                    AND ot.description LIKE \'%-%\'
        ');
        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::incomePayment()->toId().', income.id, null
            FROM operand_transaction ot
                    JOIN operand o on o.id = ot.recipient_id
                    JOIN income ON income.old_id = substr(ot.description, 23)::integer
            WHERE ot.description LIKE \'# Оплата за поставку #%\'        
                    AND ot.description NOT LIKE \'%-%\'
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::orderDebit()->toId().', orders.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN orders ON orders.id = substring(substring(ot.description FROM \'#[0-9]+\') FROM \'[0-9]+\')::integer
            WHERE ot.description LIKE \'# Начисление предоплаты%по заказу #%\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::salary()->toId().', ms.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON ot.recipient_id = o.id
                     JOIN monthly_salary ms on ms.id = substring(ot.description FROM \'[0-9]+\')::integer
            WHERE ot.description LIKE \'# Начисление ежемесячного оклада #%\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::orderPayment()->toId().', orders.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN orders ON orders.id = substring(ot.description FROM \'[0-9]+\')::integer
            WHERE ot.description LIKE \'# Cписание по заказу%\'        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::orderDebit()->toId().', orders.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN orders ON orders.id = substring(ot.description FROM \'[0-9]+\')::integer
            WHERE ot.description LIKE \'# Платеж по заказу #%\'        
        ');

        $this->addSql('DELETE FROM operand_transaction WHERE uuid IN (select id from customer_transaction)');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::orderDebit()->toId().', orders.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN orders ON orders.id = substring(ot.description FROM \'[0-9]+\')::integer
            WHERE ot.description ~ \'#\d+,.+\';        
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::penalty()->toId().', u.uuid, penalty.description
            FROM penalty
                     JOIN operand_transaction ot ON ot.id = penalty.transaction_id
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN users u ON ot.created_by_id = u.id
        ');

        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::orderDebit()->toId().', orders.uuid, null
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN orders ON orders.id = substring(ot.description FROM \'[0-9]+\')::integer
            WHERE ot.description ~ \'^#\d+$\'        
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::orderPrepay()->toId().', orders.uuid, null
            FROM wallet_transaction_old wt
                    JOIN orders ON orders.id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description LIKE \'# Аванс по заказу #%\'
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::incomePayment()->toId().', income.id, null
            FROM wallet_transaction_old wt
                    JOIN income ON income.id = substr(wt.description, 23)::uuid
            WHERE wt.description LIKE \'# Оплата за поставку%\' 
                    AND wt.description LIKE \'%-%\'
        ');
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::incomePayment()->toId().', income.id, null
            FROM wallet_transaction_old wt
                    JOIN income ON income.old_id = substr(wt.description, 23)::integer
            WHERE wt.description LIKE \'# Оплата за поставку%\'
                    AND wt.description NOT LIKE \'%-%\'
        ');
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::incomePayment()->toId().', income.id, null
            FROM wallet_transaction_old wt
                    JOIN income on income.old_id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description LIKE \'# Списание по поступлению #%\'
        ');

        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::expense()->toId().', e.uuid, ei.description
            FROM expense_item ei
                     JOIN expense e ON e.id = ei.expense_id
                     JOIN wallet_transaction_old wt ON wt.created_at = ei.created_at        
        ');

        $this->addSql('DELETE FROM wallet_transaction_old WHERE uuid IN (select id from wallet_transaction)');
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::orderPrepay()->toId().', orders.uuid, null
            FROM wallet_transaction_old wt
                    JOIN orders ON orders.id = substring(wt.description FROM \'[0-9]+\')::integer
            WHERE wt.description ~ \'#\d+,\'
        ');

        $this->addSql('DROP TABLE penalty');
        $this->addSql('DELETE FROM wallet_transaction_old WHERE uuid IN (select id from wallet_transaction)');
        $this->addSql('DELETE FROM operand_transaction WHERE uuid IN (select id from customer_transaction)');

        // Last manual customer_transactions
        $this->addSql('
            INSERT INTO customer_transaction (id, operand_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT ot.uuid, o.uuid, ot.amount_amount, ot.amount_currency_code, '.CustomerSource::manual()->toId().', u.uuid, ot.description
            FROM operand_transaction ot
                     JOIN operand o ON o.id = ot.recipient_id
                     JOIN users u ON ot.created_by_id = u.id
        ');
        // Legacy wallet transactions
        $this->addSql('
            INSERT INTO wallet_transaction (id, wallet_id, amount_amount, amount_currency_code, source, source_id, description)
            SELECT wt.uuid, wt.wallet_id, wt.amount_amount, wt.amount_currency_code, '.WalletSource::legacy()->toId().', \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\', wt.description
            FROM wallet_transaction_old wt        
        ');
        // data migration

        $this->addSql('DELETE FROM wallet_transaction_old WHERE uuid IN (select id from wallet_transaction)');
        $this->addSql('DELETE FROM operand_transaction WHERE uuid IN (select id from customer_transaction)');

        $this->addSql('DROP TABLE operand_transaction');
        $this->addSql('DROP TABLE wallet_transaction_old');
        $this->addSql('DROP TABLE expense_item');
        $this->addSql('DROP TABLE salary');
        $this->addSql('DROP TABLE IF EXISTS penalty');

        $this->addSql(BalanceView::VIEW);
        $this->addSql(WalletTransactionView::VIEW);
        $this->addSql(CustomerTransactionView::VIEW);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer_transaction');
    }
}
