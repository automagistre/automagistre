<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

final class Version20210910174511 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_permission (
          id UUID NOT NULL,
          user_id UUID NOT NULL,
          tenant_id SMALLINT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN user_permission.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_permission.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_permission.tenant_id IS \'(DC2Type:tenant_enum)\'');

        foreach ($this->connection->fetchAllAssociative('SELECT id, tenant_id FROM users') as ['id' => $userId, 'tenant_id' => $tenant]) {
            $permissionId = Uuid::uuid6()->toString();
            $this->addSql('INSERT INTO user_permission VALUES (:id, :userId, :tenantId)', [
                'id' => $permissionId,
                'userId' => $userId,
                'tenantId' => $tenant,
            ]);

            $this->addSql('UPDATE created_by SET id = :id WHERE id = :oldId', [
                'id' => $permissionId,
                'oldId' => $userId,
            ]);
        }

        $this->addSql('DROP TABLE users');
    }
}
