<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109202044 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status SMALLINT NOT NULL COMMENT \'(DC2Type:order_status_enum)\', mileage INT UNSIGNED DEFAULT NULL, description LONGTEXT DEFAULT NULL, appointment_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', closed_balance_amount VARCHAR(255) DEFAULT NULL, closed_balance_currency_code VARCHAR(3) DEFAULT NULL, car_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', customer_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', type INT NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), INDEX IDX_52EA1F09727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_service (id INT NOT NULL, service VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', warranty TINYINT(1) NOT NULL, worker_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, UNIQUE INDEX UNIQ_EE0028ECD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_part (id INT NOT NULL, quantity INT NOT NULL, warranty TINYINT(1) NOT NULL, part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income_part (id INT AUTO_INCREMENT NOT NULL, income_id INT DEFAULT NULL, accrued_motion_id INT DEFAULT NULL, quantity INT NOT NULL, part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', price_amount VARCHAR(255) DEFAULT NULL, price_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_834566E8640ED2C0 (income_id), INDEX IDX_834566E8FFE2C7 (accrued_motion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, document VARCHAR(255) DEFAULT NULL, accrued_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', supplier_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', accrued_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, ratio INT NOT NULL, hired_at DATETIME NOT NULL, fired_at DATETIME DEFAULT NULL, person_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', money_amount VARCHAR(255) DEFAULT NULL, money_currency_code VARCHAR(3) DEFAULT NULL, created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', INDEX IDX_9B522D468D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, description TEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', part_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_income (id INT NOT NULL, income_part_id INT NOT NULL, INDEX IDX_6228A7C1F4A13D95 (income_part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_report (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, INDEX IDX_7A067518D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_note (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type_enum)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', INDEX IDX_824CC0038D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_order (id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_1DF780628D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, use_in_income TINYINT(1) NOT NULL, use_in_order TINYINT(1) NOT NULL, show_in_layout TINYINT(1) NOT NULL, default_in_manual_transaction TINYINT(1) NOT NULL, currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_manual (id INT NOT NULL, user_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet_transaction (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description TEXT DEFAULT NULL, amount_amount VARCHAR(255) DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, subtotal_amount VARCHAR(255) DEFAULT NULL, subtotal_currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_7DAF972E92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_old (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operand_transaction (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description TEXT DEFAULT NULL, recipient_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', amount_amount VARCHAR(255) DEFAULT NULL, amount_currency_code VARCHAR(3) DEFAULT NULL, subtotal_amount VARCHAR(255) DEFAULT NULL, subtotal_currency_code VARCHAR(3) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, order_item_part_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_42C84955437EF9D2 (order_item_part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_suspend (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, till DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reason VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', INDEX IDX_C789F0D18D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id)');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8FFE2C7 FOREIGN KEY (accrued_motion_id) REFERENCES motion_income (id)');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE motion_income ADD CONSTRAINT FK_6228A7C1F4A13D95 FOREIGN KEY (income_part_id) REFERENCES income_part (id)');
        $this->addSql('ALTER TABLE motion_income ADD CONSTRAINT FK_6228A7C1BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_report ADD CONSTRAINT FK_7A067518D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT FK_824CC0038D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_item_group ADD CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motion_order ADD CONSTRAINT FK_1DF780628D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE motion_order ADD CONSTRAINT FK_1DF78062BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motion_manual ADD CONSTRAINT FK_4D5B7BD5BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF972E92F8F78 FOREIGN KEY (recipient_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE motion_old ADD CONSTRAINT FK_FEAF593FBF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id)');
        $this->addSql('ALTER TABLE order_suspend ADD CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE order_report DROP FOREIGN KEY FK_7A067518D9F6D38');
        $this->addSql('ALTER TABLE order_note DROP FOREIGN KEY FK_824CC0038D9F6D38');
        $this->addSql('ALTER TABLE motion_order DROP FOREIGN KEY FK_1DF780628D9F6D38');
        $this->addSql('ALTER TABLE order_suspend DROP FOREIGN KEY FK_C789F0D18D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_service DROP FOREIGN KEY FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE order_item_part DROP FOREIGN KEY FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item_group DROP FOREIGN KEY FK_F4BDA240BF396750');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955437EF9D2');
        $this->addSql('ALTER TABLE motion_income DROP FOREIGN KEY FK_6228A7C1F4A13D95');
        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8640ED2C0');
        $this->addSql('ALTER TABLE motion_income DROP FOREIGN KEY FK_6228A7C1BF396750');
        $this->addSql('ALTER TABLE motion_order DROP FOREIGN KEY FK_1DF78062BF396750');
        $this->addSql('ALTER TABLE motion_manual DROP FOREIGN KEY FK_4D5B7BD5BF396750');
        $this->addSql('ALTER TABLE motion_old DROP FOREIGN KEY FK_FEAF593FBF396750');
        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8FFE2C7');
        $this->addSql('ALTER TABLE wallet_transaction DROP FOREIGN KEY FK_7DAF972E92F8F78');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE motion');
        $this->addSql('DROP TABLE motion_income');
        $this->addSql('DROP TABLE order_report');
        $this->addSql('DROP TABLE order_note');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE motion_manual');
        $this->addSql('DROP TABLE wallet_transaction');
        $this->addSql('DROP TABLE motion_old');
        $this->addSql('DROP TABLE operand_transaction');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE order_suspend');
    }
}
