<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180917072016 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF21A26530A');
        $this->addSql('UPDATE car_recommendation SET realization_id = NULL');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF21A26530A FOREIGN KEY (realization_id) REFERENCES order_item_service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF21A26530A');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF21A26530A FOREIGN KEY (realization_id) REFERENCES orders (id)');
    }
}
