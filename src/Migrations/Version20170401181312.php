<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170401181312 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT IGNORE INTO service (name) SELECT job_advice.name FROM job_advice');

        $this->addSql('RENAME TABLE job_advice TO car_recommendation');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_3486230CC3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation ADD service_id INT DEFAULT NULL, ADD realization_id INT DEFAULT NULL, ADD expired_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, CHANGE cost cost INT NOT NULL');

        $this->addSql('UPDATE car_recommendation r SET r.service_id = (SELECT service.id FROM service WHERE service.name = r.name)');
        $this->addSql('UPDATE car_recommendation r SET r.created_at = NOW()');
        $this->addSql('UPDATE car_recommendation r SET expired_at = NOW() WHERE r.expired IS TRUE');
        $this->addSql('ALTER TABLE car_recommendation DROP name, DROP expired');

        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF21A26530A FOREIGN KEY (realization_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2ED5CA9E6 ON car_recommendation (service_id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF21A26530A ON car_recommendation (realization_id)');
        $this->addSql('DROP INDEX idx_3486230cc3c6f69f ON car_recommendation');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2C3C6F69F ON car_recommendation (car_id)');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_3486230CC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
