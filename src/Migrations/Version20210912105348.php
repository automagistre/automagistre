<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Tenant\Entity\GroupId;
use App\Tenant\Entity\TenantId;
use App\Tenant\Enum\Group;
use App\Tenant\Enum\Tenant;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912105348 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tenant_group (id UUID NOT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tenant_group.id IS \'(DC2Type:tenant_group_id)\'');
        $this->addSql('CREATE TABLE tenant (
          id UUID NOT NULL,
          group_id UUID NOT NULL,
          identifier VARCHAR(255) NOT NULL,
          display_name VARCHAR(255) NOT NULL,
          PRIMARY KEY(id, group_id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E59C462772E836A ON tenant (identifier)');
        $this->addSql('COMMENT ON COLUMN tenant.id IS \'(DC2Type:tenant_id)\'');
        $this->addSql('COMMENT ON COLUMN tenant.group_id IS \'(DC2Type:tenant_group_id)\'');

        foreach (Group::all() as $group) {
            $groupId = GroupId::generate();

            $this->addSql('INSERT INTO tenant_group (id, identifier) VALUES (:id, :identifier)', [
                'id' => $groupId->toString(),
                'identifier' => $group->toName(),
            ]);

            foreach (Tenant::all() as $tenant) {
                if ($tenant->toGroup()->eq($group)) {
                    $this->addSql('INSERT INTO tenant (id, group_id, identifier, display_name) VALUES (:id, :group, :identifier, :display_name)', [
                        'id' => TenantId::generate(),
                        'group' => $groupId->toString(),
                        'identifier' => $tenant->toName(),
                        'display_name' => $tenant->toDisplayName(),
                    ]);
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tenant_group');
        $this->addSql('DROP TABLE tenant');
    }
}
