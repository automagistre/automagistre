<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210911132533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE cron_job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cron_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_number_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appeal_calculator (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          note VARCHAR(255) DEFAULT NULL,
          phone VARCHAR(35) NOT NULL,
          date DATE DEFAULT NULL,
          equipment_id UUID NOT NULL,
          mileage INT NOT NULL,
          total BIGINT NOT NULL,
          works JSON NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.equipment_id IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.total IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.works IS \'(DC2Type:appeal_calculator_work)\'');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_call (
          id UUID NOT NULL,
          phone VARCHAR(35) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_call.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_call.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN appeal_call.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_cooperation (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          phone VARCHAR(35) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_postpone (
          id UUID NOT NULL,
          appeal_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_postpone.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appeal_postpone.appeal_id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_postpone.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_question (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          email VARCHAR(255) NOT NULL,
          question TEXT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_question.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_question.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_schedule (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          phone VARCHAR(35) NOT NULL,
          date DATE NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_status (
          id UUID NOT NULL,
          appeal_id UUID NOT NULL,
          status SMALLINT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appeal_status.appeal_id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_status.status IS \'(DC2Type:appeal_status)\'');
        $this->addSql('COMMENT ON COLUMN appeal_status.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE appeal_tire_fitting (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          phone VARCHAR(35) NOT NULL,
          model_id UUID DEFAULT NULL,
          category SMALLINT NOT NULL,
          diameter INT DEFAULT NULL,
          total BIGINT NOT NULL,
          works JSON NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.model_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.category IS \'(DC2Type:tire_fitting_category)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.total IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.works IS \'(DC2Type:appeal_tire_fitting_work)\'');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE calendar_entry (id UUID NOT NULL, tenant_id SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN calendar_entry.id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE calendar_entry_deletion (
          id UUID NOT NULL,
          entry_id UUID NOT NULL,
          reason SMALLINT NOT NULL,
          description TEXT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F118663DBA364942 ON calendar_entry_deletion (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.reason IS \'(DC2Type:deletion_reason)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_deletion.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE calendar_entry_order (
          id UUID NOT NULL,
          entry_id UUID NOT NULL,
          order_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE calendar_entry_order_info (
          id UUID NOT NULL,
          entry_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          customer_id UUID DEFAULT NULL,
          car_id UUID DEFAULT NULL,
          description TEXT DEFAULT NULL,
          worker_id UUID DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5FBDE1C1BA364942 ON calendar_entry_order_info (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order_info.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('CREATE TABLE calendar_entry_schedule (
          id UUID NOT NULL,
          entry_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          duration VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_86FDAEE3BA364942 ON calendar_entry_schedule (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('CREATE TABLE car (
          id UUID NOT NULL,
          vehicle_id UUID DEFAULT NULL,
          identifier VARCHAR(17) DEFAULT NULL,
          year INT DEFAULT NULL,
          case_type SMALLINT NOT NULL,
          description TEXT DEFAULT NULL,
          mileage INT NOT NULL,
          gosnomer VARCHAR(255) DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          equipment_transmission SMALLINT NOT NULL,
          equipment_wheel_drive SMALLINT NOT NULL,
          equipment_engine_name VARCHAR(255) DEFAULT NULL,
          equipment_engine_type SMALLINT NOT NULL,
          equipment_engine_air_intake SMALLINT NOT NULL,
          equipment_engine_injection SMALLINT NOT NULL,
          equipment_engine_capacity VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A9033212A ON car (identifier, tenant_id)');
        $this->addSql('COMMENT ON COLUMN car.id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN car.case_type IS \'(DC2Type:carcase_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN car.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('CREATE TABLE car_recommendation (
          id UUID NOT NULL,
          car_id UUID DEFAULT NULL,
          service VARCHAR(255) NOT NULL,
          worker_id UUID NOT NULL,
          expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          realization UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2C3C6F69F ON car_recommendation (car_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation.id IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE car_recommendation_part (
          id UUID NOT NULL,
          recommendation_id UUID NOT NULL,
          part_id UUID NOT NULL,
          quantity INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_DDC72D65D173940B ON car_recommendation_part (recommendation_id)');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.id IS \'(DC2Type:recommendation_part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.recommendation_id IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE created_by (
          id UUID NOT NULL,
          user_id UUID NOT NULL,
          created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN created_by.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN created_by.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN created_by.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN created_by.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE cron_job (
          id INT NOT NULL,
          name VARCHAR(191) NOT NULL,
          command VARCHAR(1024) NOT NULL,
          schedule VARCHAR(191) NOT NULL,
          description VARCHAR(191) NOT NULL,
          enabled BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX un_name ON cron_job (name)');
        $this->addSql('CREATE TABLE cron_report (
          id INT NOT NULL,
          job_id INT DEFAULT NULL,
          run_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          run_time DOUBLE PRECISION NOT NULL,
          exit_code INT NOT NULL,
          output TEXT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
        $this->addSql('CREATE TABLE customer_transaction (
          id UUID NOT NULL,
          operand_id UUID NOT NULL,
          source SMALLINT NOT NULL,
          source_id UUID NOT NULL,
          description TEXT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          amount_amount BIGINT DEFAULT NULL,
          amount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN customer_transaction.id IS \'(DC2Type:customer_transaction_id)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.operand_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.source IS \'(DC2Type:operand_transaction_source)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_transaction.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE employee (
          id UUID NOT NULL,
          person_id UUID NOT NULL,
          ratio INT NOT NULL,
          hired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          fired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN employee.id IS \'(DC2Type:employee_id)\'');
        $this->addSql('COMMENT ON COLUMN employee.person_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN employee.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE employee_salary (
          id UUID NOT NULL,
          employee_id UUID NOT NULL,
          payday INT NOT NULL,
          amount BIGINT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN employee_salary.id IS \'(DC2Type:salary_id)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary.employee_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary.amount IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE employee_salary_end (
          id UUID NOT NULL,
          salary_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59455A58B0FDF16E ON employee_salary_end (salary_id)');
        $this->addSql('COMMENT ON COLUMN employee_salary_end.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary_end.salary_id IS \'(DC2Type:salary_id)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary_end.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE expense (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          wallet_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN expense.id IS \'(DC2Type:expense_id)\'');
        $this->addSql('COMMENT ON COLUMN expense.wallet_id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN expense.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE google_review_token (
          id UUID NOT NULL,
          payload JSON NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN google_review_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN google_review_token.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE income (
          id UUID NOT NULL,
          supplier_id UUID NOT NULL,
          document VARCHAR(255) DEFAULT NULL,
          old_id INT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN income.id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income.supplier_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN income.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE income_accrue (
          id UUID NOT NULL,
          income_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          amount_amount BIGINT DEFAULT NULL,
          amount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_425DFA41640ED2C0 ON income_accrue (income_id)');
        $this->addSql('COMMENT ON COLUMN income_accrue.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN income_accrue.income_id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income_accrue.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE income_part (
          id UUID NOT NULL,
          income_id UUID DEFAULT NULL,
          part_id UUID NOT NULL,
          quantity INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_834566E8640ED2C0 ON income_part (income_id)');
        $this->addSql('COMMENT ON COLUMN income_part.id IS \'(DC2Type:income_part_id)\'');
        $this->addSql('COMMENT ON COLUMN income_part.income_id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN income_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE inventorization (id UUID NOT NULL, tenant_id SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN inventorization.id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE inventorization_close (
          id UUID NOT NULL,
          inventorization_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F6195A04CA655FD ON inventorization_close (inventorization_id)');
        $this->addSql('COMMENT ON COLUMN inventorization_close.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_close.inventorization_id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_close.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE inventorization_part (
          inventorization_id UUID NOT NULL,
          part_id UUID NOT NULL,
          quantity INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(inventorization_id, part_id)
        )');
        $this->addSql('COMMENT ON COLUMN inventorization_part.inventorization_id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE manufacturer (
          id UUID NOT NULL,
          name VARCHAR(64) NOT NULL,
          localized_name VARCHAR(255) DEFAULT NULL,
          logo VARCHAR(25) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DC5E237E06 ON manufacturer (name)');
        $this->addSql('COMMENT ON COLUMN manufacturer.id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE mc_equipment (
          id UUID NOT NULL,
          vehicle_id UUID NOT NULL,
          period INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          equipment_transmission SMALLINT NOT NULL,
          equipment_wheel_drive SMALLINT NOT NULL,
          equipment_engine_name VARCHAR(255) DEFAULT NULL,
          equipment_engine_type SMALLINT NOT NULL,
          equipment_engine_air_intake SMALLINT NOT NULL,
          equipment_engine_injection SMALLINT NOT NULL,
          equipment_engine_capacity VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN mc_equipment.id IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_transmission IS \'(DC2Type:car_transmission_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_wheel_drive IS \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_type IS \'(DC2Type:engine_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_air_intake IS \'(DC2Type:engine_air_intake)\'');
        $this->addSql('COMMENT ON COLUMN mc_equipment.equipment_engine_injection IS \'(DC2Type:engine_injection)\'');
        $this->addSql('CREATE TABLE mc_line (
          id UUID NOT NULL,
          equipment_id UUID DEFAULT NULL,
          work_id UUID DEFAULT NULL,
          period INT NOT NULL,
          recommended BOOLEAN NOT NULL,
          position INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B37EBC5F517FE9FE ON mc_line (equipment_id)');
        $this->addSql('CREATE INDEX IDX_B37EBC5FBB3453DB ON mc_line (work_id)');
        $this->addSql('COMMENT ON COLUMN mc_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_line.equipment_id IS \'(DC2Type:mc_equipment_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_line.work_id IS \'(DC2Type:mc_work_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_line.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE mc_part (
          id UUID NOT NULL,
          line_id UUID DEFAULT NULL,
          part_id UUID NOT NULL,
          quantity INT NOT NULL,
          recommended BOOLEAN NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2B65786F4D7B7542 ON mc_part (line_id)');
        $this->addSql('COMMENT ON COLUMN mc_part.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.line_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE mc_work (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          description VARCHAR(255) DEFAULT NULL,
          comment VARCHAR(255) DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN mc_work.id IS \'(DC2Type:mc_work_id)\'');
        $this->addSql('COMMENT ON COLUMN mc_work.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE motion (
          id UUID NOT NULL,
          part_id UUID DEFAULT NULL,
          quantity INT NOT NULL,
          description TEXT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          source_type SMALLINT NOT NULL,
          source_id UUID NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC ON motion (part_id)');
        $this->addSql('COMMENT ON COLUMN motion.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN motion.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('COMMENT ON COLUMN motion.source_type IS \'(DC2Type:motion_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN motion.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE note (
          id UUID NOT NULL,
          subject UUID NOT NULL,
          type SMALLINT NOT NULL,
          text TEXT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN note.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note.subject IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN note.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE note_delete (
          id UUID NOT NULL,
          note_id UUID DEFAULT NULL,
          description VARCHAR(255) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_22C02B5326ED0855 ON note_delete (note_id)');
        $this->addSql('COMMENT ON COLUMN note_delete.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note_delete.note_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note_delete.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE order_cancel (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_cancel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE order_close (
          id UUID NOT NULL,
          order_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          type VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_909FF5398D9F6D38 ON order_close (order_id)');
        $this->addSql('COMMENT ON COLUMN order_close.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_close.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_close.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE order_deal (
          id UUID NOT NULL,
          balance BIGINT NOT NULL,
          satisfaction SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN order_deal.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_deal.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN order_deal.satisfaction IS \'(DC2Type:order_satisfaction_enum)\'');
        $this->addSql('CREATE TABLE order_item (
          id UUID NOT NULL,
          order_id UUID DEFAULT NULL,
          parent_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          type VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09727ACA70 ON order_item (parent_id)');
        $this->addSql('COMMENT ON COLUMN order_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_item.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_item.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_item.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE order_item_group (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          hide_parts BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN order_item_group.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE order_item_part (
          id UUID NOT NULL,
          supplier_id UUID DEFAULT NULL,
          part_id UUID NOT NULL,
          quantity INT NOT NULL,
          warranty BOOLEAN NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          discount_amount BIGINT DEFAULT NULL,
          discount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN order_item_part.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_item_part.supplier_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN order_item_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE TABLE order_item_service (
          id UUID NOT NULL,
          service VARCHAR(255) NOT NULL,
          worker_id UUID DEFAULT NULL,
          warranty BOOLEAN NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          discount_amount BIGINT DEFAULT NULL,
          discount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN order_item_service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_item_service.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE TABLE order_payment (
          id UUID NOT NULL,
          order_id UUID DEFAULT NULL,
          description VARCHAR(255) DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          money_amount BIGINT DEFAULT NULL,
          money_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9B522D468D9F6D38 ON order_payment (order_id)');
        $this->addSql('COMMENT ON COLUMN order_payment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_payment.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_payment.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE order_suspend (
          id UUID NOT NULL,
          order_id UUID DEFAULT NULL,
          till TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          reason VARCHAR(255) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C789F0D18D9F6D38 ON order_suspend (order_id)');
        $this->addSql('COMMENT ON COLUMN order_suspend.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.till IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE orders (
          id UUID NOT NULL,
          worker_id UUID DEFAULT NULL,
          number VARCHAR(255) NOT NULL,
          status SMALLINT NOT NULL,
          car_id UUID DEFAULT NULL,
          customer_id UUID DEFAULT NULL,
          mileage INT DEFAULT NULL,
          description TEXT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6B20BA36 ON orders (worker_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE96901F549033212A ON orders (number, tenant_id)');
        $this->addSql('COMMENT ON COLUMN orders.id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.status IS \'(DC2Type:order_status_enum)\'');
        $this->addSql('COMMENT ON COLUMN orders.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE organization (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          address VARCHAR(255) DEFAULT NULL,
          telephone VARCHAR(35) DEFAULT NULL,
          office_phone VARCHAR(35) DEFAULT NULL,
          email VARCHAR(255) DEFAULT NULL,
          contractor BOOLEAN NOT NULL,
          seller BOOLEAN NOT NULL,
          tenant_id SMALLINT NOT NULL,
          requisite_bank VARCHAR(255) DEFAULT NULL,
          requisite_legal_address VARCHAR(255) DEFAULT NULL,
          requisite_ogrn VARCHAR(255) DEFAULT NULL,
          requisite_inn VARCHAR(255) DEFAULT NULL,
          requisite_kpp VARCHAR(255) DEFAULT NULL,
          requisite_rs VARCHAR(255) DEFAULT NULL,
          requisite_ks VARCHAR(255) DEFAULT NULL,
          requisite_bik VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN organization.id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN organization.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN organization.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN organization.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part (
          id UUID NOT NULL,
          manufacturer_id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          number VARCHAR(30) NOT NULL,
          universal BOOLEAN NOT NULL,
          unit SMALLINT NOT NULL,
          warehouse_id UUID DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D ON part (number, manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('COMMENT ON COLUMN part.number IS \'(DC2Type:part_number)\'');
        $this->addSql('COMMENT ON COLUMN part.unit IS \'(DC2Type:unit_enum)\'');
        $this->addSql('COMMENT ON COLUMN part.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE part_case (
          id UUID NOT NULL,
          part_id UUID NOT NULL,
          vehicle_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D19033212A ON part_case (part_id, vehicle_id, tenant_id)');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_cross_part (
          part_cross_id UUID NOT NULL,
          part_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(part_cross_id, part_id)
        )');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_cross_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_cross_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_discount (
          id UUID NOT NULL,
          part_id UUID NOT NULL,
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          tenant_id SMALLINT NOT NULL,
          discount_amount BIGINT DEFAULT NULL,
          discount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_76B231714CE34BEC ON part_discount (part_id)');
        $this->addSql('COMMENT ON COLUMN part_discount.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.since IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_price (
          id UUID NOT NULL,
          part_id UUID NOT NULL,
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          tenant_id SMALLINT NOT NULL,
          price_amount BIGINT DEFAULT NULL,
          price_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_59BB753A4CE34BEC ON part_price (part_id)');
        $this->addSql('COMMENT ON COLUMN part_price.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_price.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_price.since IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN part_price.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_required_availability (
          id UUID NOT NULL,
          part_id UUID NOT NULL,
          order_from_quantity INT NOT NULL,
          order_up_to_quantity INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_required_availability.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_required_availability.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_required_availability.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE part_supply (
          id UUID NOT NULL,
          part_id UUID NOT NULL,
          supplier_id UUID NOT NULL,
          quantity INT NOT NULL,
          source SMALLINT NOT NULL,
          source_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_supply.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.supplier_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.source IS \'(DC2Type:part_supply_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE person (
          id UUID NOT NULL,
          firstname VARCHAR(32) DEFAULT NULL,
          lastname VARCHAR(255) DEFAULT NULL,
          telephone VARCHAR(35) DEFAULT NULL,
          office_phone VARCHAR(35) DEFAULT NULL,
          email VARCHAR(255) DEFAULT NULL,
          contractor BOOLEAN NOT NULL,
          seller BOOLEAN NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176450FF010 ON person (telephone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176450FF0109033212A ON person (telephone, tenant_id)');
        $this->addSql('COMMENT ON COLUMN person.id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN person.telephone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN person.office_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN person.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE publish (
          id UUID NOT NULL,
          entity_id UUID NOT NULL,
          published BOOLEAN NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN publish.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN publish.entity_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN publish.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE reservation (
          id UUID NOT NULL,
          order_item_part_id UUID NOT NULL,
          quantity INT NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_42C84955437EF9D2 ON reservation (order_item_part_id)');
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:reservation_id)\'');
        $this->addSql('COMMENT ON COLUMN reservation.order_item_part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservation.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE review (
          id UUID NOT NULL,
          source_id VARCHAR(255) NOT NULL,
          source SMALLINT NOT NULL,
          author VARCHAR(255) NOT NULL,
          text TEXT NOT NULL,
          rating SMALLINT NOT NULL,
          publish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          raw JSON NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C65F8A7F73953C1C619033212A ON review (source, source_id, tenant_id)');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:review_id)\'');
        $this->addSql('COMMENT ON COLUMN review.source IS \'(DC2Type:review_source)\'');
        $this->addSql('COMMENT ON COLUMN review.rating IS \'(DC2Type:review_star_rating)\'');
        $this->addSql('COMMENT ON COLUMN review.publish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE sms (
          id UUID NOT NULL,
          phone_number VARCHAR(35) NOT NULL,
          message VARCHAR(255) NOT NULL,
          date_send TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN sms.id IS \'(DC2Type:sms_id)\'');
        $this->addSql('COMMENT ON COLUMN sms.phone_number IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN sms.date_send IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sms.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE sms_send (
          id UUID NOT NULL,
          sms_id UUID NOT NULL,
          success BOOLEAN NOT NULL,
          payload JSON NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN sms_send.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_send.sms_id IS \'(DC2Type:sms_id)\'');
        $this->addSql('COMMENT ON COLUMN sms_send.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE sms_status (
          id UUID NOT NULL,
          sms_id UUID NOT NULL,
          payload JSON NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN sms_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_status.sms_id IS \'(DC2Type:sms_id)\'');
        $this->addSql('CREATE TABLE storage_part (id UUID NOT NULL, tenant_id SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN storage_part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN storage_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE user_permission (
          id UUID NOT NULL,
          user_id UUID NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN user_permission.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_permission.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_permission.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE vehicle_model (
          id UUID NOT NULL,
          manufacturer_id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          localized_name VARCHAR(255) DEFAULT NULL,
          case_name VARCHAR(255) DEFAULT NULL,
          year_from SMALLINT DEFAULT NULL,
          year_till SMALLINT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B5 ON vehicle_model (manufacturer_id, name, case_name)');
        $this->addSql('COMMENT ON COLUMN vehicle_model.id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN vehicle_model.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
        $this->addSql('CREATE TABLE wallet (
          id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          use_in_income BOOLEAN NOT NULL,
          use_in_order BOOLEAN NOT NULL,
          show_in_layout BOOLEAN NOT NULL,
          default_in_manual_transaction BOOLEAN NOT NULL,
          tenant_id SMALLINT NOT NULL,
          currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN wallet.id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE wallet_transaction (
          id UUID NOT NULL,
          wallet_id UUID NOT NULL,
          source SMALLINT NOT NULL,
          source_id UUID NOT NULL,
          description TEXT DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          amount_amount BIGINT DEFAULT NULL,
          amount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.id IS \'(DC2Type:wallet_transaction_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.wallet_id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.source IS \'(DC2Type:wallet_transaction_source)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.source_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE warehouse (id UUID NOT NULL, tenant_id SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse.id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE warehouse_code (
          id UUID NOT NULL,
          warehouse_id UUID NOT NULL,
          code VARCHAR(255) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN warehouse_code.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_code.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_code.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE warehouse_name (
          id UUID NOT NULL,
          warehouse_id UUID NOT NULL,
          name VARCHAR(255) NOT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN warehouse_name.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_name.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_name.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE TABLE warehouse_parent (
          id UUID NOT NULL,
          warehouse_id UUID NOT NULL,
          warehouse_parent_id UUID DEFAULT NULL,
          tenant_id SMALLINT NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_parent_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('ALTER TABLE
          calendar_entry_deletion
        ADD
          CONSTRAINT FK_F118663DBA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          calendar_entry_order_info
        ADD
          CONSTRAINT FK_5FBDE1C1BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          calendar_entry_schedule
        ADD
          CONSTRAINT FK_86FDAEE3BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          car_recommendation
        ADD
          CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          car_recommendation_part
        ADD
          CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          cron_report
        ADD
          CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          employee_salary_end
        ADD
          CONSTRAINT FK_59455A58B0FDF16E FOREIGN KEY (salary_id) REFERENCES employee_salary (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          income_accrue
        ADD
          CONSTRAINT FK_425DFA41640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          income_part
        ADD
          CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          inventorization_close
        ADD
          CONSTRAINT FK_4F6195A04CA655FD FOREIGN KEY (inventorization_id) REFERENCES inventorization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          mc_line
        ADD
          CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          mc_line
        ADD
          CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          mc_part
        ADD
          CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          motion
        ADD
          CONSTRAINT FK_F5FEA1E84CE34BEC FOREIGN KEY (part_id) REFERENCES storage_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          note_delete
        ADD
          CONSTRAINT FK_22C02B5326ED0855 FOREIGN KEY (note_id) REFERENCES note (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_cancel
        ADD
          CONSTRAINT FK_9599D5A7BF396750 FOREIGN KEY (id) REFERENCES order_close (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_close
        ADD
          CONSTRAINT FK_909FF5398D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_deal
        ADD
          CONSTRAINT FK_AE0FFB01BF396750 FOREIGN KEY (id) REFERENCES order_close (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_item
        ADD
          CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_item
        ADD
          CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_item_group
        ADD
          CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_item_part
        ADD
          CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_item_service
        ADD
          CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_payment
        ADD
          CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          order_suspend
        ADD
          CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          orders
        ADD
          CONSTRAINT FK_E52FFDEE6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          reservation
        ADD
          CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar_entry_deletion DROP CONSTRAINT FK_F118663DBA364942');
        $this->addSql('ALTER TABLE calendar_entry_order_info DROP CONSTRAINT FK_5FBDE1C1BA364942');
        $this->addSql('ALTER TABLE calendar_entry_schedule DROP CONSTRAINT FK_86FDAEE3BA364942');
        $this->addSql('ALTER TABLE car_recommendation DROP CONSTRAINT FK_8E4BAAF2C3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation_part DROP CONSTRAINT FK_DDC72D65D173940B');
        $this->addSql('ALTER TABLE cron_report DROP CONSTRAINT FK_B6C6A7F5BE04EA9');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6B20BA36');
        $this->addSql('ALTER TABLE employee_salary_end DROP CONSTRAINT FK_59455A58B0FDF16E');
        $this->addSql('ALTER TABLE income_accrue DROP CONSTRAINT FK_425DFA41640ED2C0');
        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT FK_834566E8640ED2C0');
        $this->addSql('ALTER TABLE inventorization_close DROP CONSTRAINT FK_4F6195A04CA655FD');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5F517FE9FE');
        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT FK_2B65786F4D7B7542');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE note_delete DROP CONSTRAINT FK_22C02B5326ED0855');
        $this->addSql('ALTER TABLE order_cancel DROP CONSTRAINT FK_9599D5A7BF396750');
        $this->addSql('ALTER TABLE order_deal DROP CONSTRAINT FK_AE0FFB01BF396750');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_group DROP CONSTRAINT FK_F4BDA240BF396750');
        $this->addSql('ALTER TABLE order_item_part DROP CONSTRAINT FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item_service DROP CONSTRAINT FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955437EF9D2');
        $this->addSql('ALTER TABLE order_close DROP CONSTRAINT FK_909FF5398D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE order_suspend DROP CONSTRAINT FK_C789F0D18D9F6D38');
        $this->addSql('ALTER TABLE motion DROP CONSTRAINT FK_F5FEA1E84CE34BEC');
        $this->addSql('DROP SEQUENCE cron_job_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE cron_report_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_number_seq CASCADE');
        $this->addSql('DROP TABLE appeal_calculator');
        $this->addSql('DROP TABLE appeal_call');
        $this->addSql('DROP TABLE appeal_cooperation');
        $this->addSql('DROP TABLE appeal_postpone');
        $this->addSql('DROP TABLE appeal_question');
        $this->addSql('DROP TABLE appeal_schedule');
        $this->addSql('DROP TABLE appeal_status');
        $this->addSql('DROP TABLE appeal_tire_fitting');
        $this->addSql('DROP TABLE calendar_entry');
        $this->addSql('DROP TABLE calendar_entry_deletion');
        $this->addSql('DROP TABLE calendar_entry_order');
        $this->addSql('DROP TABLE calendar_entry_order_info');
        $this->addSql('DROP TABLE calendar_entry_schedule');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE created_by');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
        $this->addSql('DROP TABLE customer_transaction');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE employee_salary');
        $this->addSql('DROP TABLE employee_salary_end');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE google_review_token');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE income_accrue');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE inventorization');
        $this->addSql('DROP TABLE inventorization_close');
        $this->addSql('DROP TABLE inventorization_part');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_part');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE motion');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE note_delete');
        $this->addSql('DROP TABLE order_cancel');
        $this->addSql('DROP TABLE order_close');
        $this->addSql('DROP TABLE order_deal');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE order_suspend');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_case');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE part_discount');
        $this->addSql('DROP TABLE part_price');
        $this->addSql('DROP TABLE part_required_availability');
        $this->addSql('DROP TABLE part_supply');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE publish');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE sms');
        $this->addSql('DROP TABLE sms_send');
        $this->addSql('DROP TABLE sms_status');
        $this->addSql('DROP TABLE storage_part');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE vehicle_model');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_transaction');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_code');
        $this->addSql('DROP TABLE warehouse_name');
        $this->addSql('DROP TABLE warehouse_parent');
    }
}
