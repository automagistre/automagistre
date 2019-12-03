<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190114154518 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE orders ADD closed_by_id INT DEFAULT NULL, ADD car_id INT DEFAULT NULL, ADD customer_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_service ADD worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part ADD part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part ADD part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE income ADD supplier_id INT DEFAULT NULL, ADD accrued_by_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_payment ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion ADD part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_note ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense_item ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion_manual ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operand_transaction ADD recipient_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_suspend ADD created_by_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE employee DROP person_id');
        $this->addSql('ALTER TABLE expense_item DROP created_by_id');
        $this->addSql('ALTER TABLE income DROP supplier_id, DROP accrued_by_id, DROP created_by_id');
        $this->addSql('ALTER TABLE income_part DROP part_id');
        $this->addSql('ALTER TABLE motion DROP part_id');
        $this->addSql('ALTER TABLE motion_manual DROP user_id');
        $this->addSql('ALTER TABLE operand_transaction DROP recipient_id, DROP created_by_id');
        $this->addSql('ALTER TABLE order_item DROP created_by_id');
        $this->addSql('ALTER TABLE order_item_part DROP part_id');
        $this->addSql('ALTER TABLE order_item_service DROP worker_id');
        $this->addSql('ALTER TABLE order_note DROP created_by_id');
        $this->addSql('ALTER TABLE order_payment DROP created_by_id');
        $this->addSql('ALTER TABLE order_suspend DROP created_by_id');
        $this->addSql('ALTER TABLE orders DROP closed_by_id, DROP car_id, DROP customer_id, DROP created_by_id');
        $this->addSql('ALTER TABLE wallet_transaction DROP created_by_id');
    }
}
