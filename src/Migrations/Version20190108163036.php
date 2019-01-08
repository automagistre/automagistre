<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190108163036 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->skipIf('landlord' !== $this->connection->getDatabase());

        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8640ED2C0');
        $this->addSql('ALTER TABLE motion_income DROP FOREIGN KEY FK_6228A7C1F4A13D95');
        $this->addSql('ALTER TABLE motion_income DROP FOREIGN KEY FK_6228A7C1BF396750');
        $this->addSql('ALTER TABLE motion_manual DROP FOREIGN KEY FK_4D5B7BD5BF396750');
        $this->addSql('ALTER TABLE motion_old DROP FOREIGN KEY FK_FEAF593FBF396750');
        $this->addSql('ALTER TABLE motion_order DROP FOREIGN KEY FK_1DF78062BF396750');
        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8FFE2C7');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_group DROP FOREIGN KEY FK_F4BDA240BF396750');
        $this->addSql('ALTER TABLE order_item_part DROP FOREIGN KEY FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item_service DROP FOREIGN KEY FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955437EF9D2');
        $this->addSql('ALTER TABLE motion_order DROP FOREIGN KEY FK_1DF780628D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_note DROP FOREIGN KEY FK_CFBDFA148D9F6D38');
        $this->addSql('ALTER TABLE order_payment DROP FOREIGN KEY FK_9B522D468D9F6D38');
        $this->addSql('ALTER TABLE order_report DROP FOREIGN KEY FK_7A067518D9F6D38');
        $this->addSql('ALTER TABLE order_suspend DROP FOREIGN KEY FK_C789F0D18D9F6D38');
        $this->addSql('ALTER TABLE wallet_transaction DROP FOREIGN KEY FK_7DAF972E92F8F78');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE income');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE motion');
        $this->addSql('DROP TABLE motion_income');
        $this->addSql('DROP TABLE motion_manual');
        $this->addSql('DROP TABLE motion_old');
        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE operand_transaction');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE order_note');
        $this->addSql('DROP TABLE order_payment');
        $this->addSql('DROP TABLE order_report');
        $this->addSql('DROP TABLE order_suspend');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE partner_operand');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_transaction');

        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, database_host VARCHAR(255) NOT NULL, database_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E59C462772E836A ON tenant (identifier)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(true, 'Rollback impossible');
    }
}
