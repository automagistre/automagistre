<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Employee\Entity\SalaryView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200622194556 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE monthly_salary_id_seq CASCADE');
        $this->addSql('CREATE TABLE employee_salary (id UUID NOT NULL, employee_id UUID NOT NULL, payday INT NOT NULL, amount VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN employee_salary.id IS \'(DC2Type:salary_id)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary.employee_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary.amount IS \'(DC2Type:money)\'');
        $this->addSql('CREATE TABLE employee_salary_end (id UUID NOT NULL, salary_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59455A58B0FDF16E ON employee_salary_end (salary_id)');
        $this->addSql('COMMENT ON COLUMN employee_salary_end.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN employee_salary_end.salary_id IS \'(DC2Type:salary_id)\'');
        $this->addSql('ALTER TABLE employee_salary_end ADD CONSTRAINT FK_59455A58B0FDF16E FOREIGN KEY (salary_id) REFERENCES employee_salary (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // data migration
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT monthly_salary.uuid, u.uuid, monthly_salary.created_at FROM monthly_salary
            JOIN users u ON u.id = monthly_salary.created_by_id
        ');
        $this->addSql('
            INSERT INTO employee_salary (id, employee_id, payday, amount) 
            SELECT ms.uuid AS id,
                   e.uuid AS employee_id,
                   ms.payday AS payday,
                   ms.amount_currency_code || \' \' || ms.amount_amount AS amount
            FROM monthly_salary ms
                JOIN employee e ON e.id = ms.employee_id
            ORDER BY ms.id
        ');
        // data migration

        $this->addSql(SalaryView::sql());
        $this->addSql('DROP TABLE monthly_salary');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE employee_salary_end DROP CONSTRAINT FK_59455A58B0FDF16E');
        $this->addSql('CREATE SEQUENCE monthly_salary_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE monthly_salary (id SERIAL NOT NULL, employee_id INT DEFAULT NULL, payday INT NOT NULL, ended_at DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount_amount BIGINT DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, ended_by_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_77b328ba8c03f15c ON monthly_salary (employee_id)');
        $this->addSql('COMMENT ON COLUMN monthly_salary.ended_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN monthly_salary.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN monthly_salary.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE monthly_salary ADD CONSTRAINT fk_77b328ba8c03f15c FOREIGN KEY (employee_id) REFERENCES employee (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE employee_salary');
        $this->addSql('DROP TABLE employee_salary_end');
    }
}
