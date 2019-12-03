<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190116091221 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE order_item_service ADD discount_amount VARCHAR(255) DEFAULT NULL, ADD discount_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part ADD discount_amount VARCHAR(255) DEFAULT NULL, ADD discount_currency_code VARCHAR(3) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item_part DROP discount_amount, DROP discount_currency_code');
        $this->addSql('ALTER TABLE order_item_service DROP discount_amount, DROP discount_currency_code');
    }
}
