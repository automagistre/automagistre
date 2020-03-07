<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200307211437 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE TABLE orders (
          id SERIAL NOT NULL, 
          worker_id INT DEFAULT NULL, 
          closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          status SMALLINT NOT NULL, 
          mileage INT DEFAULT NULL, 
          description TEXT DEFAULT NULL, 
          appointment_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          closed_by_id INT DEFAULT NULL, 
          closed_balance_amount VARCHAR(255) DEFAULT NULL, 
          closed_balance_currency_code VARCHAR(3) DEFAULT NULL, 
          car_id INT DEFAULT NULL, 
          customer_id INT DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6B20BA36 ON orders (worker_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE5D13417F ON orders (closed_at)');
        $this->addSql('COMMENT ON COLUMN orders.closed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN orders.status IS \'(DC2Type:order_status_enum)\'');
        $this->addSql('COMMENT ON COLUMN orders.appointment_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE motion_old (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE motion (
          id SERIAL NOT NULL, 
          quantity INT NOT NULL, 
          description TEXT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          part_id INT NOT NULL, 
          type INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC ON motion (part_id)');
        $this->addSql('COMMENT ON COLUMN motion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_payment (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          description VARCHAR(255) DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          money_amount VARCHAR(255) DEFAULT NULL, 
          money_currency_code VARCHAR(3) DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9B522D468D9F6D38 ON order_payment (order_id)');
        $this->addSql('COMMENT ON COLUMN order_payment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE motion_manual (id INT NOT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE penalty (
          id SERIAL NOT NULL, 
          transaction_id INT DEFAULT NULL, 
          description VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_AFE28FD82FC0CB0F ON penalty (transaction_id)');
        $this->addSql('CREATE TABLE reservation (
          id SERIAL NOT NULL, 
          order_item_part_id INT NOT NULL, 
          quantity INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_42C84955437EF9D2 ON reservation (order_item_part_id)');
        $this->addSql('CREATE TABLE order_item (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          parent_id INT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by_id INT DEFAULT NULL, 
          type INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09727ACA70 ON order_item (parent_id)');
        $this->addSql('COMMENT ON COLUMN order_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_item_part (
          id INT NOT NULL, 
          quantity INT NOT NULL, 
          warranty BOOLEAN NOT NULL, 
          supplier_id INT DEFAULT NULL, 
          part_id INT NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          discount_amount VARCHAR(255) DEFAULT NULL, 
          discount_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE order_contractor (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          contractor_id INT DEFAULT NULL, 
          money_amount VARCHAR(255) DEFAULT NULL, 
          money_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F0A12FBA8D9F6D38 ON order_contractor (order_id)');
        $this->addSql('CREATE TABLE income (
          id SERIAL NOT NULL, 
          document VARCHAR(255) DEFAULT NULL, 
          accrued_at DATE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          supplier_id INT DEFAULT NULL, 
          accrued_by_id INT DEFAULT NULL, 
          accrued_amount_amount VARCHAR(255) DEFAULT NULL, 
          accrued_amount_currency_code VARCHAR(3) DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_3FA862D06FC55E56 ON income (accrued_at)');
        $this->addSql('COMMENT ON COLUMN income.accrued_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN income.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_suspend (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          till TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          reason VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C789F0D18D9F6D38 ON order_suspend (order_id)');
        $this->addSql('COMMENT ON COLUMN order_suspend.till IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE expense (
          id SERIAL NOT NULL, 
          wallet_id INT DEFAULT NULL, 
          name VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6712520F3 ON expense (wallet_id)');
        $this->addSql('CREATE TABLE employee (
          id SERIAL NOT NULL, 
          ratio INT NOT NULL, 
          hired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          fired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          person_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE wallet_transaction (
          id SERIAL NOT NULL, 
          recipient_id INT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          description TEXT NOT NULL, 
          amount_amount VARCHAR(255) DEFAULT NULL, 
          amount_currency_code VARCHAR(3) DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7DAF972E92F8F78 ON wallet_transaction (recipient_id)');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE expense_item (
          id SERIAL NOT NULL, 
          expense_id INT DEFAULT NULL, 
          description VARCHAR(255) DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          amount_amount VARCHAR(255) DEFAULT NULL, 
          amount_currency_code VARCHAR(3) DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_ABBC6B7CF395DB7B ON expense_item (expense_id)');
        $this->addSql('COMMENT ON COLUMN expense_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE monthly_salary (
          id SERIAL NOT NULL, 
          employee_id INT DEFAULT NULL, 
          payday INT NOT NULL, 
          ended_at DATE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          amount_amount VARCHAR(255) DEFAULT NULL, 
          amount_currency_code VARCHAR(3) DEFAULT NULL, 
          ended_by_id INT DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_77B328BA8C03F15C ON monthly_salary (employee_id)');
        $this->addSql('COMMENT ON COLUMN monthly_salary.ended_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN monthly_salary.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_item_service (
          id INT NOT NULL, 
          service VARCHAR(255) NOT NULL, 
          warranty BOOLEAN NOT NULL, 
          worker_id INT DEFAULT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          discount_amount VARCHAR(255) DEFAULT NULL, 
          discount_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE salary (
          id SERIAL NOT NULL, 
          income_id INT DEFAULT NULL, 
          outcome_id INT DEFAULT NULL, 
          description VARCHAR(255) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9413BB71640ED2C0 ON salary (income_id)');
        $this->addSql('CREATE INDEX IDX_9413BB71E6EE6D63 ON salary (outcome_id)');
        $this->addSql('CREATE TABLE motion_income (id INT NOT NULL, income_part_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6228A7C1F4A13D95 ON motion_income (income_part_id)');
        $this->addSql('CREATE TABLE motion_order (id INT NOT NULL, order_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DF780628D9F6D38 ON motion_order (order_id)');
        $this->addSql('CREATE TABLE wallet (
          id SERIAL NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          use_in_income BOOLEAN NOT NULL, 
          use_in_order BOOLEAN NOT NULL, 
          show_in_layout BOOLEAN NOT NULL, 
          default_in_manual_transaction BOOLEAN NOT NULL, 
          currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE operand_transaction (
          id SERIAL NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          description TEXT NOT NULL, 
          recipient_id INT DEFAULT NULL, 
          amount_amount VARCHAR(255) DEFAULT NULL, 
          amount_currency_code VARCHAR(3) DEFAULT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN operand_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_note (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          type SMALLINT NOT NULL, 
          text TEXT NOT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_824CC0038D9F6D38 ON order_note (order_id)');
        $this->addSql('COMMENT ON COLUMN order_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('CREATE TABLE income_part (
          id SERIAL NOT NULL, 
          income_id INT DEFAULT NULL, 
          accrued_motion_id INT DEFAULT NULL, 
          quantity INT NOT NULL, 
          part_id INT NOT NULL, 
          price_amount VARCHAR(255) DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_834566E8640ED2C0 ON income_part (income_id)');
        $this->addSql('CREATE INDEX IDX_834566E8FFE2C7 ON income_part (accrued_motion_id)');
        $this->addSql('CREATE TABLE order_salary (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          transaction_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_579CABA48D9F6D38 ON order_salary (order_id)');
        $this->addSql('CREATE INDEX IDX_579CABA42FC0CB0F ON order_salary (transaction_id)');
        $this->addSql('CREATE TABLE order_item_group (
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          hide_parts BOOLEAN NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE appointment (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          duration VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_FE38F8448D9F6D38 ON appointment (order_id)');
        $this->addSql('COMMENT ON COLUMN appointment.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appointment.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN appointment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE 
          orders 
        ADD 
          CONSTRAINT FK_E52FFDEE6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_old 
        ADD 
          CONSTRAINT FK_FEAF593FBF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_payment 
        ADD 
          CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_manual 
        ADD 
          CONSTRAINT FK_4D5B7BD5BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          penalty 
        ADD 
          CONSTRAINT FK_AFE28FD82FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          reservation 
        ADD 
          CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_item 
        ADD 
          CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_item 
        ADD 
          CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_item_part 
        ADD 
          CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_contractor 
        ADD 
          CONSTRAINT FK_F0A12FBA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_suspend 
        ADD 
          CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          expense 
        ADD 
          CONSTRAINT FK_2D3A8DA6712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          wallet_transaction 
        ADD 
          CONSTRAINT FK_7DAF972E92F8F78 FOREIGN KEY (recipient_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          expense_item 
        ADD 
          CONSTRAINT FK_ABBC6B7CF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          monthly_salary 
        ADD 
          CONSTRAINT FK_77B328BA8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_item_service 
        ADD 
          CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          salary 
        ADD 
          CONSTRAINT FK_9413BB71640ED2C0 FOREIGN KEY (income_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          salary 
        ADD 
          CONSTRAINT FK_9413BB71E6EE6D63 FOREIGN KEY (outcome_id) REFERENCES wallet_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_income 
        ADD 
          CONSTRAINT FK_6228A7C1F4A13D95 FOREIGN KEY (income_part_id) REFERENCES income_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_income 
        ADD 
          CONSTRAINT FK_6228A7C1BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_order 
        ADD 
          CONSTRAINT FK_1DF780628D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          motion_order 
        ADD 
          CONSTRAINT FK_1DF78062BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_note 
        ADD 
          CONSTRAINT FK_824CC0038D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          income_part 
        ADD 
          CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          income_part 
        ADD 
          CONSTRAINT FK_834566E8FFE2C7 FOREIGN KEY (accrued_motion_id) REFERENCES motion_income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_salary 
        ADD 
          CONSTRAINT FK_579CABA48D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_salary 
        ADD 
          CONSTRAINT FK_579CABA42FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          order_item_group 
        ADD 
          CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          appointment 
        ADD 
          CONSTRAINT FK_FE38F8448D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_contractor DROP CONSTRAINT FK_F0A12FBA8D9F6D38');
        $this->addSql('ALTER TABLE order_suspend DROP CONSTRAINT FK_C789F0D18D9F6D38');
        $this->addSql('ALTER TABLE motion_order DROP CONSTRAINT FK_1DF780628D9F6D38');
        $this->addSql('ALTER TABLE order_note DROP CONSTRAINT FK_824CC0038D9F6D38');
        $this->addSql('ALTER TABLE order_salary DROP CONSTRAINT FK_579CABA48D9F6D38');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F8448D9F6D38');
        $this->addSql('ALTER TABLE motion_old DROP CONSTRAINT FK_FEAF593FBF396750');
        $this->addSql('ALTER TABLE motion_manual DROP CONSTRAINT FK_4D5B7BD5BF396750');
        $this->addSql('ALTER TABLE motion_income DROP CONSTRAINT FK_6228A7C1BF396750');
        $this->addSql('ALTER TABLE motion_order DROP CONSTRAINT FK_1DF78062BF396750');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_part DROP CONSTRAINT FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item_service DROP CONSTRAINT FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE order_item_group DROP CONSTRAINT FK_F4BDA240BF396750');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955437EF9D2');
        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT FK_834566E8640ED2C0');
        $this->addSql('ALTER TABLE expense_item DROP CONSTRAINT FK_ABBC6B7CF395DB7B');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6B20BA36');
        $this->addSql('ALTER TABLE monthly_salary DROP CONSTRAINT FK_77B328BA8C03F15C');
        $this->addSql('ALTER TABLE salary DROP CONSTRAINT FK_9413BB71E6EE6D63');
        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT FK_834566E8FFE2C7');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA6712520F3');
        $this->addSql('ALTER TABLE wallet_transaction DROP CONSTRAINT FK_7DAF972E92F8F78');
        $this->addSql('ALTER TABLE penalty DROP CONSTRAINT FK_AFE28FD82FC0CB0F');
        $this->addSql('ALTER TABLE salary DROP CONSTRAINT FK_9413BB71640ED2C0');
        $this->addSql('ALTER TABLE order_salary DROP CONSTRAINT FK_579CABA42FC0CB0F');
        $this->addSql('ALTER TABLE motion_income DROP CONSTRAINT FK_6228A7C1F4A13D95');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE motion_old');
        $this->addSql('DROP TABLE motion');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE motion_manual');
        $this->addSql('DROP TABLE penalty');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE order_contractor');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE order_suspend');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE wallet_transaction');
        $this->addSql('DROP TABLE expense_item');
        $this->addSql('DROP TABLE monthly_salary');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE salary');
        $this->addSql('DROP TABLE motion_income');
        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE operand_transaction');
        $this->addSql('DROP TABLE order_note');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE order_salary');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('DROP TABLE appointment');
    }
}
