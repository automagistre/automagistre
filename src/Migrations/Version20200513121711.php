<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function strpos;

final class Version20200513121711 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        //> Data migration
        $cars = $this->connection->fetchAll('
            SELECT car.uuid AS car_id, o.uuid AS possessor_id 
            FROM car
            JOIN operand o on car.owner_id = o.id
            ORDER BY car.id
        ');

        $cars = array_map(
            fn (array $row) => sprintf(
                '(\'%s\'::uuid, \'%s\'::uuid, \'%s\'::uuid, 1)',
                Uuid::uuid6()->toString(),
                $row['possessor_id'],
                $row['car_id']
            ),
            $cars
        );
        if ([] !== $cars) {
            $cars = implode(', ', $cars);
            $this->addSql('INSERT INTO car_possession (id, possessor_id, car_id, transition) VALUES '.$cars);
        }
        //< Data migration

        $this->addSql('ALTER TABLE car DROP CONSTRAINT fk_773de69d7e3c61f9');
        $this->addSql('DROP INDEX idx_773de69d7e3c61f9');
        $this->addSql('ALTER TABLE car DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE car ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE 
          car 
        ADD 
          CONSTRAINT fk_773de69d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_773de69d7e3c61f9 ON car (owner_id)');
    }
}
