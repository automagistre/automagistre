<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170411153727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation ADD worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF26B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF26B20BA36 ON car_recommendation (worker_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF26B20BA36');
        $this->addSql('DROP INDEX IDX_8E4BAAF26B20BA36 ON car_recommendation');
        $this->addSql('ALTER TABLE car_recommendation DROP worker_id');
    }
}
