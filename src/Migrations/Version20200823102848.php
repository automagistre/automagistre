<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_key_exists;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;

final class Version20200823102848 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE manufacturer ALTER name SET NOT NULL');

        $duplicatesManufacturers = $this->connection->fetchAll('
        SELECT m1.id AS from, m2.id AS to
        FROM manufacturer m1
                 JOIN manufacturer m2 ON m1.name = m2.name AND m1.id <> m2.id        
        ');

        $fromTo = [];
        foreach ($duplicatesManufacturers as $row) {
            if (!array_key_exists($row['to'], $fromTo)) {
                $fromTo[$row['from']] = $row['to'];
            }
        }

        foreach ($fromTo as $manufacturerFrom => $manufacturerTo) {
            $duplicateParts = $this->connection->fetchAll(
                "SELECT p1.id AS from, p2.id AS to
                FROM part p1
                JOIN part p2 ON p1.number = p2.number
                    WHERE p1.manufacturer_id = '{$manufacturerFrom}' AND p2.manufacturer_id = '{$manufacturerTo}'
                "
            );

            foreach ($duplicateParts as $duplicatePart) {
                ['from' => $partFrom, 'to' => $partTo] = $duplicatePart;

                $this->addSql("UPDATE order_item_part SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE motion SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE part_case SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("DELETE FROM part_cross_part WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE part_discount SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE part_price SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE part_required_availability SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE part_supply SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE mc_part SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE income_part SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");
                $this->addSql("UPDATE car_recommendation_part SET part_id = '{$partTo}' WHERE part_id = '{$partFrom}'");

                $this->addSql("DELETE FROM part WHERE id = '{$partFrom}'");
            }

            $this->addSql(sprintf('UPDATE part SET manufacturer_id = \'%s\' WHERE manufacturer_id = \'%s\'', $manufacturerTo, $manufacturerFrom));
            $this->addSql(sprintf('UPDATE vehicle_model SET manufacturer_id = \'%s\' WHERE manufacturer_id = \'%s\'', $manufacturerTo, $manufacturerFrom));
            $this->addSql(sprintf('DELETE FROM manufacturer WHERE id = \'%s\'', $manufacturerFrom));
        }

        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DC5E237E06 ON manufacturer (name)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_3D0AE6DC5E237E06');
        $this->addSql('ALTER TABLE manufacturer ALTER name DROP NOT NULL');
    }
}
