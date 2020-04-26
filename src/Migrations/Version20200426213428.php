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

final class Version20200426213428 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE manufacturer ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN manufacturer.uuid IS \'(DC2Type:manufacturer_id)\'');
    }

    public function postUp(Schema $schema): void
    {
        $conn = $this->connection;

        $stmt = $conn->executeQuery('SELECT id from manufacturer ORDER BY id');
        $values = $stmt->fetchAll();
        if ([] !== $values) {
            $values = array_map(fn (array $row) => sprintf("(%s, '%s'::uuid)", $row['id'], Uuid::uuid6()->toString()), $values);
            $values = implode(', ', $values);
            $conn->executeQuery("UPDATE manufacturer as t SET uuid = v.uuid FROM (values {$values}) AS v(id, uuid) WHERE t.id = v.id");
        }

        $conn->executeQuery('ALTER TABLE manufacturer ALTER uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE manufacturer DROP uuid');
    }
}
