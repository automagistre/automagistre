<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190114193255 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE orders DROP car_uuid, DROP customer_uuid, DROP closed_by_uuid, DROP created_by_uuid');
        $this->addSql('ALTER TABLE order_item DROP created_by_uuid');
        $this->addSql('ALTER TABLE income_part DROP part_uuid');
        $this->addSql('ALTER TABLE wallet_transaction DROP created_by_uuid');
        $this->addSql('ALTER TABLE motion DROP part_uuid');
        $this->addSql('ALTER TABLE motion_manual DROP user_uuid');
        $this->addSql('ALTER TABLE order_note DROP created_by_uuid');
        $this->addSql('ALTER TABLE income DROP supplier_uuid, DROP accrued_by_uuid, DROP created_by_uuid');
        $this->addSql('ALTER TABLE employee DROP person_uuid');
        $this->addSql('ALTER TABLE order_item_part DROP part_uuid');
        $this->addSql('ALTER TABLE order_suspend DROP created_by_uuid');
        $this->addSql('DROP INDEX UNIQ_EE0028ECD17F50A6 ON order_item_service');
        $this->addSql('ALTER TABLE order_item_service DROP uuid, DROP worker_uuid');
        $this->addSql('ALTER TABLE operand_transaction DROP recipient_uuid, DROP created_by_uuid');
        $this->addSql('ALTER TABLE expense_item DROP created_by_uuid');
        $this->addSql('ALTER TABLE order_payment DROP created_by_uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE employee ADD person_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE expense_item ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE income ADD supplier_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD accrued_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE income_part ADD part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE motion ADD part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE motion_manual ADD user_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE operand_transaction ADD recipient_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_item ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_item_part ADD part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_item_service ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD worker_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EE0028ECD17F50A6 ON order_item_service (uuid)');
        $this->addSql('ALTER TABLE order_note ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_payment ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_suspend ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE orders ADD car_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD customer_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD closed_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE wallet_transaction ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
    }
}
