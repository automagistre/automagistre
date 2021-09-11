<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Tenant\Enum\Tenant;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function is_string;

final class Version20210911162611 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_773de69d772e836a9033212a');
        $this->addSql('ALTER TABLE car RENAME COLUMN tenant_id TO tenant_group_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836ADFF2BBB0 ON car (identifier, tenant_group_id)');
        $this->addSql('ALTER TABLE car_recommendation RENAME COLUMN tenant_id TO tenant_group_id');
        $this->addSql('ALTER TABLE car_recommendation_part RENAME COLUMN tenant_id TO tenant_group_id');
        $this->addSql('ALTER TABLE organization RENAME COLUMN tenant_id TO tenant_group_id');
        $this->addSql('DROP INDEX uniq_34dcd176450ff0109033212a');
        $this->addSql('ALTER TABLE person RENAME COLUMN tenant_id TO tenant_group_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176450FF010DFF2BBB0 ON person (telephone, tenant_group_id)');

        $identifier = getenv('TENANT');

        if (!is_string($identifier)) {
            return;
        }

        $tenant = Tenant::fromIdentifier($identifier);
        $group = $tenant->toGroup();

        $this->addSql('UPDATE car SET tenant_group_id = :group WHERE tenant_group_id = :tenant', [
            'group' => $group->toId(),
            'tenant' => $tenant->toId(),
        ]);
        $this->addSql('UPDATE car_recommendation SET tenant_group_id = :group WHERE tenant_group_id = :tenant', [
            'group' => $group->toId(),
            'tenant' => $tenant->toId(),
        ]);
        $this->addSql('UPDATE car_recommendation_part SET tenant_group_id = :group WHERE tenant_group_id = :tenant', [
            'group' => $group->toId(),
            'tenant' => $tenant->toId(),
        ]);
        $this->addSql('UPDATE organization SET tenant_group_id = :group WHERE tenant_group_id = :tenant', [
            'group' => $group->toId(),
            'tenant' => $tenant->toId(),
        ]);
        $this->addSql('UPDATE person SET tenant_group_id = :group WHERE tenant_group_id = :tenant', [
            'group' => $group->toId(),
            'tenant' => $tenant->toId(),
        ]);
    }
}
