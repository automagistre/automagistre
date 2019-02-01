<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131152654 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('CREATE TABLE monthly_salary (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, payday INT NOT NULL, ended_at DATE NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount_amount VARCHAR(255) DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, ended_by_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, INDEX IDX_77B328BA8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monthly_salary ADD CONSTRAINT FK_77B328BA8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('DROP TABLE monthly_salary');
    }
}
