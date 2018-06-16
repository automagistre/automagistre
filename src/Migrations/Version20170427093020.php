<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class Version20170427093020 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        /* Set Automagistre as default worker */
        $this->addSql('UPDATE car_recommendation SET worker_id = 1 WHERE worker_id IS NULL');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF26B20BA36');
        $this->addSql('ALTER TABLE car_recommendation MODIFY worker_id INT(11) NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF26B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');

        $this->addSql('UPDATE service SET price = price * 100 WHERE price > 0');
        $this->addSql('UPDATE car_recommendation SET cost = cost * 100 WHERE cost > 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE service SET price = price / 100 WHERE price > 0');
        $this->addSql('UPDATE car_recommendation SET cost = cost / 100 WHERE cost > 0');
    }
}
