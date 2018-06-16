<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170401190122 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE car_recommendation_part (id INT AUTO_INCREMENT NOT NULL, recommendation_id INT DEFAULT NULL, part_id INT DEFAULT NULL, quantity INT NOT NULL, cost INT NOT NULL, INDEX IDX_DDC72D65D173940B (recommendation_id), INDEX IDX_DDC72D654CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id)');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D654CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE car_recommendation_part');
    }
}
