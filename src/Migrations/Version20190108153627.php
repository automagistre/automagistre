<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Version20190108153627 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation_part CHANGE created_by_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE car_note CHANGE created_by_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE created_by_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE operand_note CHANGE created_by_id created_by_id INT NOT NULL');

        /* Create unique UUIDs */

        $this->addSql('ALTER TABLE car ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE car SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON car (uuid)');

        $this->addSql('ALTER TABLE users ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE users SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');

        $this->addSql('ALTER TABLE operand ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE operand SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');

        $this->addSql('ALTER TABLE part ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE part SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C6D17F50A6 ON part (uuid)');

        $this->addSql('ALTER TABLE manufacturer ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE manufacturer SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DCD17F50A6 ON manufacturer (uuid)');

        $this->addSql('ALTER TABLE order_item_service ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', ADD worker_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('UPDATE order_item_service SET uuid=(UNHEX(REPLACE((SELECT uuid()), "-","")))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EE0028ECD17F50A6 ON order_item_service (uuid)');

        /* Created UUIDs relations */

        $this->addSql('ALTER TABLE car_recommendation ADD realization_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', CHANGE created_by_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD car_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', ADD customer_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', ADD closed_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_item ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE income_part ADD part_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE motion ADD part_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE motion_manual ADD user_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_note ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE income ADD supplier_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', ADD accrued_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\', ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE employee ADD person_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_item_part ADD part_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_suspend ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE operand_transaction ADD recipient_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_payment ADD created_by_uuid BINARY(16) NULL COMMENT \'(DC2Type:uuid_binary)\'');

        /* Set is_contractor = 1 on employees */

        $this->addSql('UPDATE operand JOIN employee ON operand.id = employee.person_id SET contractor = 1 WHERE employee.fired_at IS NULL');

        /* Populate UUIDs */

        $this->addSql('UPDATE car_recommendation JOIN order_item_service ois on car_recommendation.realization_id = ois.id SET realization_uuid = ois.uuid');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF21A26530A');
        $this->addSql('ALTER TABLE car_recommendation DROP realization_id');

        $this->addSql('UPDATE orders JOIN car on orders.car_id = car.id SET car_uuid = car.uuid');
        $this->addSql('UPDATE orders JOIN operand customer on orders.customer_id = customer.id SET customer_uuid = customer.uuid');
        $this->addSql('UPDATE orders JOIN users closedBy on orders.closed_by_id = closedBy.id SET closed_by_uuid = closedBy.uuid');
        $this->addSql('UPDATE orders JOIN users createdBy on orders.created_by_id = createdBy.id SET created_by_uuid = createdBy.uuid');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB03A8386');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEC3C6F69F');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEE1FA7797');
        $this->addSql('ALTER TABLE orders DROP car_id, DROP customer_id, DROP closed_by_id, DROP created_by_id');

        $this->addSql('UPDATE order_item JOIN users u on order_item.created_by_id = u.id SET created_by_uuid = u.uuid');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09B03A8386');
        $this->addSql('ALTER TABLE order_item DROP created_by_id');

        $this->addSql('UPDATE income_part JOIN part p on income_part.part_id = p.id SET part_uuid = p.uuid');
        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E84CE34BEC');
        $this->addSql('ALTER TABLE income_part DROP part_id');

        $this->addSql('UPDATE motion m JOIN part p on m.part_id = p.id SET m.part_uuid = p.uuid');
        $this->addSql('ALTER TABLE motion DROP FOREIGN KEY FK_F5FEA1E89233A555');
        $this->addSql('ALTER TABLE motion DROP part_id');

        $this->addSql('UPDATE motion_manual JOIN users u on motion_manual.user_id = u.id SET user_uuid = u.uuid');
        $this->addSql('ALTER TABLE motion_manual DROP FOREIGN KEY FK_4D5B7BD5A76ED395');
        $this->addSql('ALTER TABLE motion_manual DROP user_id');

        $this->addSql('UPDATE order_note JOIN users u on order_note.created_by_id = u.id SET created_by_uuid = u.uuid');
        $this->addSql('ALTER TABLE order_note DROP FOREIGN KEY FK_824CC003B03A8386');
        $this->addSql('ALTER TABLE order_note DROP created_by_id');

        $this->addSql('UPDATE income JOIN users u on income.accrued_by_id = u.id SET accrued_by_uuid = u.uuid');
        $this->addSql('UPDATE income JOIN users u on income.created_by_id = u.id SET created_by_uuid = u.uuid');
        $this->addSql('UPDATE income JOIN operand o on income.supplier_id = o.id SET supplier_uuid = o.uuid');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D02ADD6D8C');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0748C73B5');
        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0B03A8386');
        $this->addSql('ALTER TABLE income DROP supplier_id, DROP created_by_id, DROP accrued_by_id');

        $this->addSql('UPDATE employee JOIN person p on employee.person_id = p.id JOIN operand o ON o.id = p.id SET person_uuid = o.uuid');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1217BBB47');
        $this->addSql('ALTER TABLE employee DROP person_id');

        $this->addSql('UPDATE order_item_part JOIN part p on order_item_part.part_id = p.id SET part_uuid = p.uuid');
        $this->addSql('ALTER TABLE order_item_part DROP FOREIGN KEY FK_3DB84FC54CE34BEC');
        $this->addSql('ALTER TABLE order_item_part DROP part_id');

        $this->addSql('UPDATE order_suspend JOIN users u on order_suspend.created_by_id = u.id SET created_by_uuid = u.uuid');
        $this->addSql('ALTER TABLE order_suspend DROP FOREIGN KEY FK_C789F0D1B03A8386');
        $this->addSql('ALTER TABLE order_suspend DROP created_by_id');

        $this->addSql('UPDATE order_item_service JOIN operand o on order_item_service.worker_id = o.id SET worker_uuid = o.uuid');
        $this->addSql('ALTER TABLE order_item_service DROP FOREIGN KEY FK_EE0028EC6B20BA36');
        $this->addSql('ALTER TABLE order_item_service DROP worker_id');

        $this->addSql('UPDATE operand_transaction JOIN operand o on operand_transaction.recipient_id = o.id SET recipient_uuid = o.uuid');
        $this->addSql('ALTER TABLE operand_transaction DROP FOREIGN KEY FK_6D28840DE92F8F78');
        $this->addSql('ALTER TABLE operand_transaction DROP recipient_id');

        $this->addSql('update order_payment JOIN users u on order_payment.created_by_id = u.id SET created_by_uuid = u.uuid');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D46B03A8386');
        $this->addSql('ALTER TABLE order_payment DROP created_by_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(true, 'Rollback not possible');
    }
}
