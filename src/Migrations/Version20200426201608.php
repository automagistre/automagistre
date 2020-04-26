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

final class Version20200426201608 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_model ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_model ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:vehicle_id)\'');
    }

    public function postUp(Schema $schema): void
    {
        $conn = $this->connection;

        $stmt = $conn->executeQuery('SELECT id from car_model ORDER BY id');
        $values = $stmt->fetchAll();
        if ([] !== $values) {
            $values = array_map(fn (array $row) => sprintf("(%s, '%s'::uuid)", $row['id'], Uuid::uuid6()->toString()), $values);
            $values = implode(', ', $values);
            $conn->executeQuery("UPDATE car_model as t SET uuid = v.uuid FROM (values {$values}) AS v(id, uuid) WHERE t.id = v.id");
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_model ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_model ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:uuid)\'');
    }
}
