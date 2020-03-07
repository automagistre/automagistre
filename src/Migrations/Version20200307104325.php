<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200307104325 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE SEQUENCE orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE motion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE penalty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_contractor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE income_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_suspend_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE expense_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE expense_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE monthly_salary_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE salary_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE income_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_salary_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE orders (
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
          transaction_id INT DEFAULT NULL, 
          description VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_AFE28FD82FC0CB0F ON penalty (transaction_id)');
        $this->addSql('CREATE TABLE reservation (
          id INT NOT NULL, 
          order_item_part_id INT NOT NULL, 
          quantity INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_42C84955437EF9D2 ON reservation (order_item_part_id)');
        $this->addSql('CREATE TABLE order_item (
          id INT NOT NULL, 
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
          id INT NOT NULL, 
          order_id INT DEFAULT NULL, 
          contractor_id INT DEFAULT NULL, 
          money_amount VARCHAR(255) DEFAULT NULL, 
          money_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F0A12FBA8D9F6D38 ON order_contractor (order_id)');
        $this->addSql('CREATE TABLE income (
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
          wallet_id INT DEFAULT NULL, 
          name VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6712520F3 ON expense (wallet_id)');
        $this->addSql('CREATE TABLE employee (
          id INT NOT NULL, 
          ratio INT NOT NULL, 
          hired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          fired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          person_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE wallet_transaction (
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          use_in_income BOOLEAN NOT NULL, 
          use_in_order BOOLEAN NOT NULL, 
          show_in_layout BOOLEAN NOT NULL, 
          default_in_manual_transaction BOOLEAN NOT NULL, 
          currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE operand_transaction (
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
          id INT NOT NULL, 
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
    }
}
