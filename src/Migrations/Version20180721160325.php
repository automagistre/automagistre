<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180721160325 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_modification CHANGE `case` `case` SMALLINT DEFAULT NULL COMMENT \'(DC2Type:carcase_enum)\', CHANGE transmission transmission SMALLINT DEFAULT NULL COMMENT \'(DC2Type:car_transmission_enum)\', CHANGE wheel_drive wheel_drive SMALLINT DEFAULT NULL COMMENT \'(DC2Type:car_wheel_drive_enum)\'');
        $this->addSql('ALTER TABLE orders CHANGE status status SMALLINT NOT NULL COMMENT \'(DC2Type:order_status_enum)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_modification CHANGE `case` `case` SMALLINT DEFAULT NULL, CHANGE transmission transmission SMALLINT DEFAULT NULL, CHANGE wheel_drive wheel_drive SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE status status SMALLINT NOT NULL');
    }
}
