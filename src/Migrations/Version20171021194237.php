<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171021194237 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE payment SET amount = amount * 100');
        $this->addSql('UPDATE payment SET subtotal = subtotal* 100 WHERE subtotal IS NOT NULL');
        $this->addSql('ALTER TABLE payment CHANGE amount amount VARCHAR(255) NOT NULL, CHANGE subtotal subtotal VARCHAR(255) DEFAULT NULL');

        $this->addSql('CREATE TABLE order_payment (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B522D468D9F6D38 (order_id), UNIQUE INDEX UNIQ_9B522D464C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D464C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment CHANGE amount amount DOUBLE PRECISION DEFAULT NULL, CHANGE subtotal subtotal DOUBLE PRECISION DEFAULT NULL');

        $this->addSql('DROP TABLE order_payment');
    }
}
