<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180728072855 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation ADD service VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_item_service ADD service VARCHAR(255) NOT NULL');
        $this->addSql('
          UPDATE order_item_service
            JOIN service s on order_item_service.service_id = s.id
          SET order_item_service.service = s.name
        ');

        $this->addSql('
          UPDATE car_recommendation
            JOIN service s on car_recommendation.service_id = s.id
          SET car_recommendation.service = s.name
        ');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF2ED5CA9E6');
        $this->addSql('ALTER TABLE order_item_service DROP FOREIGN KEY FK_EE0028ECED5CA9E6');
        $this->addSql('DROP INDEX IDX_EE0028ECED5CA9E6 ON order_item_service');
        $this->addSql('DROP INDEX IDX_8E4BAAF2ED5CA9E6 ON car_recommendation');

        $this->addSql('ALTER TABLE car_recommendation DROP service_id');
        $this->addSql('ALTER TABLE order_item_service DROP service_id');
        $this->addSql('DROP TABLE service');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, price INT NOT NULL, UNIQUE INDEX UNIQ_E19D9AD25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_recommendation ADD service_id INT DEFAULT NULL, DROP service');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2ED5CA9E6 ON car_recommendation (service_id)');
        $this->addSql('ALTER TABLE order_item_service ADD service_id INT DEFAULT NULL, DROP service');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_EE0028ECED5CA9E6 ON order_item_service (service_id)');
    }
}
