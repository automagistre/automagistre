<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180916162904 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation_part CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE supply CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_service CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE part CHANGE price price_amount VARCHAR(255) DEFAULT NULL, ADD price_currency_code VARCHAR(3) DEFAULT NULL');

        $this->addSql('UPDATE car_recommendation_part SET price_currency_code = "RUB"');
        $this->addSql('UPDATE supply SET price_currency_code = "RUB"');
        $this->addSql('UPDATE income_part SET price_currency_code = "RUB"');
        $this->addSql('UPDATE order_item_part SET price_currency_code = "RUB"');
        $this->addSql('UPDATE order_item_service SET price_currency_code = "RUB"');
        $this->addSql('UPDATE car_recommendation SET price_currency_code = "RUB"');
        $this->addSql('UPDATE part SET price_currency_code = "RUB"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE car_recommendation_part ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE income_part ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE order_item_part ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE order_item_service ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE part ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
        $this->addSql('ALTER TABLE supply ADD price INT NOT NULL, DROP price_amount, DROP price_currency_code');
    }
}
