<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170422233022 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('
        UPDATE car_generation
          JOIN car_model ON car_model.id = car_generation.car_model_id
        SET car_generation.name = TRIM(REPLACE(REPLACE(REPLACE(car_generation.name, car_model.name, \'\'), \'(\', \'\'), \')\', \'\'))
        WHERE car_generation.name LIKE (SELECT CONCAT(\'%\', car_model.name, \'%\'))
              AND LENGTH(car_model.name) < LENGTH(car_generation.name);
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
