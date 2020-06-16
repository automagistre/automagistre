<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200616180432 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE cron_job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cron_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE penalty (id SERIAL NOT NULL, transaction_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AFE28FD82FC0CB0F ON penalty (transaction_id)');
        $this->addSql('CREATE TABLE operand_transaction (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description TEXT NOT NULL, recipient_id INT DEFAULT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN operand_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE salary (id SERIAL NOT NULL, income_id INT DEFAULT NULL, outcome_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9413BB71640ED2C0 ON salary (income_id)');
        $this->addSql('CREATE INDEX IDX_9413BB71E6EE6D63 ON salary (outcome_id)');
        $this->addSql('CREATE TABLE cron_job (id INT NOT NULL, name VARCHAR(191) NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(191) NOT NULL, description VARCHAR(191) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX un_name ON cron_job (name)');
        $this->addSql('CREATE TABLE cron_report (id INT NOT NULL, job_id INT DEFAULT NULL, run_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, run_time DOUBLE PRECISION NOT NULL, exit_code INT NOT NULL, output TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
        $this->addSql('CREATE TABLE calendar_entry_deletion (id UUID NOT NULL, entry_id UUID NOT NULL, reason SMALLINT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F118663DBA364942 ON calendar_entry_deletion (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.reason IS \'(DC2Type:deletion_reason)\'');
        $this->addSql('CREATE TABLE calendar_entry_order (id UUID NOT NULL, entry_id UUID NOT NULL, order_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('CREATE TABLE calendar_entry (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN calendar_entry.id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('CREATE TABLE calendar_entry_schedule (id UUID NOT NULL, entry_id UUID DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86FDAEE3BA364942 ON calendar_entry_schedule (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('CREATE TABLE calendar_entry_order_info (id UUID NOT NULL, entry_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, car_id UUID DEFAULT NULL, description TEXT DEFAULT NULL, worker_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5FBDE1C1BA364942 ON calendar_entry_order_info (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('CREATE TABLE car_note (id SERIAL NOT NULL, car_id INT DEFAULT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, created_by UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4D7EEB8C3C6F69F ON car_note (car_id)');
        $this->addSql('COMMENT ON COLUMN car_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE car_recommendation_part (id SERIAL NOT NULL, recommendation_id INT DEFAULT NULL, uuid UUID NOT NULL, part_id UUID NOT NULL, quantity INT NOT NULL, created_by UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDC72D65D17F50A6 ON car_recommendation_part (uuid)');
        $this->addSql('CREATE INDEX IDX_DDC72D65D173940B ON car_recommendation_part (recommendation_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:recommendation_part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE car_recommendation (id SERIAL NOT NULL, car_id INT DEFAULT NULL, uuid UUID NOT NULL, service VARCHAR(255) NOT NULL, worker_id UUID NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, realization_id INT DEFAULT NULL, realization_tenant SMALLINT DEFAULT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E4BAAF2D17F50A6 ON car_recommendation (uuid)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2C3C6F69F ON car_recommendation (car_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization_tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE car (id SERIAL NOT NULL, uuid UUID NOT NULL, vehicle_id UUID DEFAULT NULL, identifier VARCHAR(17) DEFAULT NULL, year INT DEFAULT NULL, case_type SMALLINT NOT NULL, description TEXT DEFAULT NULL, gosnomer VARCHAR(255) DEFAULT NULL, mileage INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, equipment_transmission SMALLINT NOT NULL, equipment_wheel_drive SMALLINT NOT NULL, equipment_engine_name VARCHAR(255) DEFAULT NULL, equipment_engine_type SMALLINT NOT NULL, equipment_engine_air_intake SMALLINT DEFAULT NULL, equipment_engine_injection SMALLINT DEFAULT NULL, equipment_engine_capacity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON car (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A ON car (identifier)');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN car.case_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('CREATE TABLE created_by (id UUID NOT NULL, user_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN created_by.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN created_by.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN created_by.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE person (id INT NOT NULL, firstname VARCHAR(32) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, telephone VARCHAR(35) DEFAULT NULL, office_phone VARCHAR(35) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176450FF010 ON person (telephone)');
        $this->addSql('COMMENT ON COLUMN person.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN person.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE operand (id SERIAL NOT NULL, uuid UUID NOT NULL, email VARCHAR(255) DEFAULT NULL, contractor BOOLEAN NOT NULL, seller BOOLEAN NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE TABLE organization (id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, telephone VARCHAR(35) DEFAULT NULL, office_phone VARCHAR(35) DEFAULT NULL, requisite_bank VARCHAR(255) DEFAULT NULL, requisite_legal_address VARCHAR(255) DEFAULT NULL, requisite_ogrn VARCHAR(255) DEFAULT NULL, requisite_inn VARCHAR(255) DEFAULT NULL, requisite_kpp VARCHAR(255) DEFAULT NULL, requisite_rs VARCHAR(255) DEFAULT NULL, requisite_ks VARCHAR(255) DEFAULT NULL, requisite_bik VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN organization.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN organization.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE operand_note (id SERIAL NOT NULL, operand_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by UUID NOT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_36BDE44118D7F226 ON operand_note (operand_id)');
        $this->addSql('COMMENT ON COLUMN operand_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('CREATE TABLE monthly_salary (id SERIAL NOT NULL, employee_id INT DEFAULT NULL, payday INT NOT NULL, ended_at DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, ended_by_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77B328BA8C03F15C ON monthly_salary (employee_id)');
        $this->addSql('COMMENT ON COLUMN monthly_salary.ended_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN monthly_salary.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE employee (id SERIAL NOT NULL, uuid UUID NOT NULL, ratio INT NOT NULL, hired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, person_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN employee.uuid IS \'(DC2Type:employee_id)\'');
        $this->addSql('CREATE TABLE expense (id SERIAL NOT NULL, wallet_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6712520F3 ON expense (wallet_id)');
        $this->addSql('CREATE TABLE expense_item (id SERIAL NOT NULL, expense_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ABBC6B7CF395DB7B ON expense_item (expense_id)');
        $this->addSql('COMMENT ON COLUMN expense_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE income (id UUID NOT NULL, supplier_id UUID NOT NULL, document VARCHAR(255) DEFAULT NULL, accrued_at DATE DEFAULT NULL, accrued_by_id INT DEFAULT NULL, accrued_amount_amount BIGINT DEFAULT NULL, accrued_amount_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3FA862D06FC55E56 ON income (accrued_at)');
        $this->addSql('COMMENT ON COLUMN income.id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income.supplier_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN income.accrued_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE income_part (id SERIAL NOT NULL, income_id UUID DEFAULT NULL, part_id UUID NOT NULL, uuid UUID NOT NULL, quantity INT NOT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_834566E8640ED2C0 ON income_part (income_id)');
        $this->addSql('COMMENT ON COLUMN income_part.income_id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN income_part.uuid IS \'(DC2Type:income_part_id)\'');
        $this->addSql('CREATE TABLE mc_equipment (id SERIAL NOT NULL, uuid UUID NOT NULL, vehicle_id UUID DEFAULT NULL, period INT NOT NULL, equipment_transmission SMALLINT NOT NULL, equipment_wheel_drive SMALLINT NOT NULL, equipment_engine_name VARCHAR(255) DEFAULT NULL, equipment_engine_type SMALLINT NOT NULL, equipment_engine_air_intake SMALLINT DEFAULT NULL, equipment_engine_injection SMALLINT DEFAULT NULL, equipment_engine_capacity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN mc_equipment.uuid IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('CREATE TABLE mc_part (id SERIAL NOT NULL, line_id INT DEFAULT NULL, part_id UUID NOT NULL, quantity INT NOT NULL, recommended BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B65786F4D7B7542 ON mc_part (line_id)');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE mc_line (id SERIAL NOT NULL, equipment_id INT DEFAULT NULL, work_id INT DEFAULT NULL, period INT NOT NULL, recommended BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B37EBC5F517FE9FE ON mc_line (equipment_id)');
        $this->addSql('CREATE INDEX IDX_B37EBC5FBB3453DB ON mc_line (work_id)');
        $this->addSql('CREATE TABLE mc_work (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE manufacturer (id SERIAL NOT NULL, uuid UUID NOT NULL, name VARCHAR(64) DEFAULT NULL, localized_name VARCHAR(255) DEFAULT NULL, logo VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN manufacturer.uuid IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE order_item_part (id INT NOT NULL, part_id UUID NOT NULL, quantity INT NOT NULL, warranty BOOLEAN NOT NULL, supplier_id INT DEFAULT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, discount_amount BIGINT DEFAULT NULL, discount_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_item_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE order_item (id SERIAL NOT NULL, order_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09727ACA70 ON order_item (parent_id)');
        $this->addSql('COMMENT ON COLUMN order_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_item_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, hide_parts BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE order_note (id SERIAL NOT NULL, order_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_824CC0038D9F6D38 ON order_note (order_id)');
        $this->addSql('COMMENT ON COLUMN order_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('CREATE TABLE order_salary (id SERIAL NOT NULL, order_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_579CABA48D9F6D38 ON order_salary (order_id)');
        $this->addSql('CREATE INDEX IDX_579CABA42FC0CB0F ON order_salary (transaction_id)');
        $this->addSql('CREATE TABLE order_suspend (id SERIAL NOT NULL, order_id INT DEFAULT NULL, till TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reason VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C789F0D18D9F6D38 ON order_suspend (order_id)');
        $this->addSql('COMMENT ON COLUMN order_suspend.till IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_contractor (id SERIAL NOT NULL, order_id INT DEFAULT NULL, contractor_id INT DEFAULT NULL, money_amount BIGINT DEFAULT NULL, money_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F0A12FBA8D9F6D38 ON order_contractor (order_id)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, worker_id INT DEFAULT NULL, uuid UUID NOT NULL, closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status SMALLINT NOT NULL, car_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, mileage INT DEFAULT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, closed_by_id INT DEFAULT NULL, closed_balance_amount BIGINT DEFAULT NULL, closed_balance_currency_code VARCHAR(3) DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6B20BA36 ON orders (worker_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE5D13417F ON orders (closed_at)');
        $this->addSql('COMMENT ON COLUMN orders.uuid IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.closed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN orders.status IS \'(DC2Type:order_status_enum)\'');
        $this->addSql('COMMENT ON COLUMN orders.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_item_service (id INT NOT NULL, service VARCHAR(255) NOT NULL, worker_id UUID DEFAULT NULL, warranty BOOLEAN NOT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, discount_amount BIGINT DEFAULT NULL, discount_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_item_service.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE TABLE reservation (id SERIAL NOT NULL, order_item_part_id INT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C84955437EF9D2 ON reservation (order_item_part_id)');
        $this->addSql('CREATE TABLE order_payment (id SERIAL NOT NULL, order_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, money_amount BIGINT DEFAULT NULL, money_currency_code VARCHAR(3) DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B522D468D9F6D38 ON order_payment (order_id)');
        $this->addSql('COMMENT ON COLUMN order_payment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE part_cross (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE part_cross_part (part_cross_id INT NOT NULL, part_id UUID NOT NULL, PRIMARY KEY(part_cross_id, part_id))');
        $this->addSql('CREATE INDEX IDX_B98F499C70B9088C ON part_cross_part (part_cross_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B98F499C4CE34BEC ON part_cross_part (part_id)');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE part (id UUID NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(30) NOT NULL, universal BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D ON part (number, manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN part.number IS \'(DC2Type:part_number)\'');
        $this->addSql('CREATE TABLE part_case (id UUID NOT NULL, part_id UUID NOT NULL, vehicle_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D1 ON part_case (part_id, vehicle_id)');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('CREATE TABLE stockpile (part_id UUID NOT NULL, tenant SMALLINT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(part_id, tenant))');
        $this->addSql('CREATE INDEX IDX_C2E8923F4CE34BEC4E59C4629FF31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2E8923F4CE34BEC4E59C462 ON stockpile (part_id, tenant)');
        $this->addSql('COMMENT ON COLUMN stockpile.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN stockpile.tenant IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_price (id UUID NOT NULL, part_id UUID NOT NULL, since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price_amount BIGINT DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN part_price.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_price.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_price.since IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE part_discount (id UUID NOT NULL, part_id UUID NOT NULL, since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, discount_amount BIGINT DEFAULT NULL, discount_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN part_discount.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.since IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, author VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, content TEXT NOT NULL, source VARCHAR(255) NOT NULL, publish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN review.publish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sms_send (id UUID NOT NULL, sms_id UUID NOT NULL, success BOOLEAN NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms_send.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_send.sms_id IS \'(DC2Type:sms_id)\'');
        $this->addSql('CREATE TABLE sms_status (id UUID NOT NULL, sms_id UUID NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_status.sms_id IS \'(DC2Type:sms_id)\'');
        $this->addSql('CREATE TABLE sms (id UUID NOT NULL, phone_number VARCHAR(35) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms.id IS \'(DC2Type:sms_id)\'');
        $this->addSql('COMMENT ON COLUMN sms.phone_number IS \'(DC2Type:phone_number)\'');
        $this->addSql('CREATE TABLE motion (id SERIAL NOT NULL, quantity INT NOT NULL, part_id UUID NOT NULL, source SMALLINT NOT NULL, source_id UUID NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC8B8E8428 ON motion (part_id, created_at)');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN motion.source IS \'(DC2Type:motion_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN motion.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN motion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE warehouse (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse.id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE warehouse_name (id UUID NOT NULL, warehouse_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse_name.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_name.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE warehouse_parent (id UUID NOT NULL, warehouse_id UUID NOT NULL, warehouse_parent_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_parent_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE vehicle_model (id SERIAL NOT NULL, uuid UUID NOT NULL, manufacturer_id UUID NOT NULL, name VARCHAR(255) NOT NULL, localized_name VARCHAR(255) DEFAULT NULL, case_name VARCHAR(255) DEFAULT NULL, year_from SMALLINT DEFAULT NULL, year_till SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235D17F50A6 ON vehicle_model (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42DDF3BA4B5 ON vehicle_model (manufacturer_id, case_name)');
        $this->addSql('COMMENT ON COLUMN vehicle_model.uuid IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN vehicle_model.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE wallet (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, use_in_income BOOLEAN NOT NULL, use_in_order BOOLEAN NOT NULL, show_in_layout BOOLEAN NOT NULL, default_in_manual_transaction BOOLEAN NOT NULL, currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wallet_transaction (id SERIAL NOT NULL, recipient_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description TEXT NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, created_by_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7DAF972E92F8F78 ON wallet_transaction (recipient_id)');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE penalty ADD CONSTRAINT FK_AFE28FD82FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB71640ED2C0 FOREIGN KEY (income_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB71E6EE6D63 FOREIGN KEY (outcome_id) REFERENCES wallet_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cron_report ADD CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry_deletion ADD CONSTRAINT FK_F118663DBA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry_schedule ADD CONSTRAINT FK_86FDAEE3BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry_order_info ADD CONSTRAINT FK_5FBDE1C1BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT FK_4D7EEB8C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT FK_36BDE44118D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE monthly_salary ADD CONSTRAINT FK_77B328BA8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_item ADD CONSTRAINT FK_ABBC6B7CF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_group ADD CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT FK_824CC0038D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_salary ADD CONSTRAINT FK_579CABA48D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_salary ADD CONSTRAINT FK_579CABA42FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_suspend ADD CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_contractor ADD CONSTRAINT FK_F0A12FBA8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF972E92F8F78 FOREIGN KEY (recipient_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('
            CREATE VIEW warehouse_view AS
            SELECT root.id                AS id,
                   wn.name                AS name,
                   wp.warehouse_parent_id AS parent_id
            FROM warehouse root
                     JOIN LATERAL (SELECT name
                                   FROM warehouse_name sub
                                   WHERE sub.warehouse_id = root.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) wn ON true
                     LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                        FROM warehouse_parent sub
                                        WHERE sub.warehouse_id = root.id
                                        ORDER BY sub.id DESC
                                        LIMIT 1
                ) wp ON true
        ');
        $this->addSql('
                    CREATE VIEW calendar_entry_view AS
                    SELECT e.id,
                           ces.date         AS schedule_date,
                           ces.duration     AS schedule_duration,
                           ceoi.customer_id AS order_info_customer_id,
                           ceoi.car_id      AS order_info_car_id,
                           ceoi.description AS order_info_description,
                           ceoi.worker_id   AS order_info_worker_id,
                           ceo.order_id     AS order_id
                    FROM calendar_entry e
                             LEFT JOIN calendar_entry_deletion ced on e.id = ced.entry_id
                             LEFT JOIN calendar_entry_order ceo ON ceo.entry_id = e.id
                             JOIN LATERAL (SELECT *
                                           FROM calendar_entry_schedule sub
                                           WHERE sub.entry_id = e.id
                                           ORDER BY sub.id DESC
                                           LIMIT 1
                        ) ces ON true
                             JOIN LATERAL (SELECT *
                                           FROM calendar_entry_order_info sub
                                           WHERE sub.entry_id = e.id
                                           ORDER BY sub.id DESC
                                           LIMIT 1
                        ) ceoi ON true
                    WHERE ced IS NULL
                ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE penalty DROP CONSTRAINT FK_AFE28FD82FC0CB0F');
        $this->addSql('ALTER TABLE salary DROP CONSTRAINT FK_9413BB71640ED2C0');
        $this->addSql('ALTER TABLE order_salary DROP CONSTRAINT FK_579CABA42FC0CB0F');
        $this->addSql('ALTER TABLE cron_report DROP CONSTRAINT FK_B6C6A7F5BE04EA9');
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP CONSTRAINT FK_F118663DBA364942');
        $this->addSql('ALTER TABLE calendar_entry_schedule DROP CONSTRAINT FK_86FDAEE3BA364942');
        $this->addSql('ALTER TABLE calendar_entry_order_info DROP CONSTRAINT FK_5FBDE1C1BA364942');
        $this->addSql('ALTER TABLE car_recommendation_part DROP CONSTRAINT FK_DDC72D65D173940B');
        $this->addSql('ALTER TABLE car_note DROP CONSTRAINT FK_4D7EEB8C3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation DROP CONSTRAINT FK_8E4BAAF2C3C6F69F');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT FK_34DCD176BF396750');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE operand_note DROP CONSTRAINT FK_36BDE44118D7F226');
        $this->addSql('ALTER TABLE monthly_salary DROP CONSTRAINT FK_77B328BA8C03F15C');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6B20BA36');
        $this->addSql('ALTER TABLE expense_item DROP CONSTRAINT FK_ABBC6B7CF395DB7B');
        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT FK_834566E8640ED2C0');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5F517FE9FE');
        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT FK_2B65786F4D7B7542');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955437EF9D2');
        $this->addSql('ALTER TABLE order_item_part DROP CONSTRAINT FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_group DROP CONSTRAINT FK_F4BDA240BF396750');
        $this->addSql('ALTER TABLE order_item_service DROP CONSTRAINT FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_note DROP CONSTRAINT FK_824CC0038D9F6D38');
        $this->addSql('ALTER TABLE order_salary DROP CONSTRAINT FK_579CABA48D9F6D38');
        $this->addSql('ALTER TABLE order_suspend DROP CONSTRAINT FK_C789F0D18D9F6D38');
        $this->addSql('ALTER TABLE order_contractor DROP CONSTRAINT FK_F0A12FBA8D9F6D38');
        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT FK_B98F499C70B9088C');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT FK_B98F499C4CE34BEC');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA6712520F3');
        $this->addSql('ALTER TABLE wallet_transaction DROP CONSTRAINT FK_7DAF972E92F8F78');
        $this->addSql('ALTER TABLE salary DROP CONSTRAINT FK_9413BB71E6EE6D63');
        $this->addSql('DROP SEQUENCE cron_job_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE cron_report_id_seq CASCADE');
        $this->addSql('DROP TABLE penalty');
        $this->addSql('DROP TABLE operand_transaction');
        $this->addSql('DROP TABLE salary');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
        $this->addSql('DROP TABLE calendar_entry_deletion');
        $this->addSql('DROP TABLE calendar_entry_order');
        $this->addSql('DROP TABLE calendar_entry');
        $this->addSql('DROP TABLE calendar_entry_schedule');
        $this->addSql('DROP TABLE calendar_entry_order_info');
        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE created_by');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE monthly_salary');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_item');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE mc_part');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('DROP TABLE order_note');
        $this->addSql('DROP TABLE order_salary');
        $this->addSql('DROP TABLE order_suspend');
        $this->addSql('DROP TABLE order_contractor');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_case');
        $this->addSql('DROP TABLE stockpile');
        $this->addSql('DROP TABLE part_price');
        $this->addSql('DROP TABLE part_discount');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE sms_send');
        $this->addSql('DROP TABLE sms_status');
        $this->addSql('DROP TABLE sms');
        $this->addSql('DROP TABLE motion');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_name');
        $this->addSql('DROP TABLE warehouse_parent');
        $this->addSql('DROP TABLE vehicle_model');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_transaction');
    }
}
