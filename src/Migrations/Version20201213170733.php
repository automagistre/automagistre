<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Costil;
use function array_keys;
use DateTimeImmutable;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

final class Version20201213170733 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $ids = $this->connection->fetchAllAssociativeIndexed('SELECT id FROM mc_equipment');
        $date = new DateTimeImmutable();

        foreach (array_keys($ids) as $entityId) {
            $id = Uuid::uuid6()->toString();
            $this->addSql('INSERT INTO publish (id, entity_id, published) VALUES (:id, :entity, TRUE)', [
                'id' => $id,
                'entity' => $entityId,
            ]);
            $this->addSql('INSERT INTO created_by (id, user_id, created_at) VALUES (:id, :user, :date)', [
                'id' => $id,
                'user' => Costil::SERVICE_USER,
                'date' => $date,
            ], [
                'date' => 'datetime',
            ]);
        }
    }
}
