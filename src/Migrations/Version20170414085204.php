<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170414085204 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_part DROP FOREIGN KEY FK_4FE4AD1706C1B43');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65706C1B43');

        $this->addSql('ALTER TABLE order_part MODIFY selector_id INT DEFAULT NULL');
        $this->addSql('UPDATE order_part SET selector_id = NULL');
        $this->addSql('ALTER TABLE car_recommendation_part MODIFY selector_id INT DEFAULT NULL');
        $this->addSql('UPDATE car_recommendation_part SET selector_id = NULL');

        $this->addSql('ALTER TABLE order_part CHANGE selector_id selector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD1706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE selector_id selector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65706C1B43');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE selector_id selector_id INT NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65706C1B43 FOREIGN KEY (selector_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE order_part DROP FOREIGN KEY FK_4FE4AD1706C1B43');
        $this->addSql('ALTER TABLE order_part CHANGE selector_id selector_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD1706C1B43 FOREIGN KEY (selector_id) REFERENCES person (id)');
    }
}
