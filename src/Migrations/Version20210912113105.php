<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912113105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_2a0e7894ce34bec545317d19033212a');
        $this->addSql('ALTER TABLE part_case DROP tenant_id');

        $this->addSql('CREATE TEMP TABLE _part_case AS SELECT * FROM part_case');
        $this->addSql('TRUNCATE part_case');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D1 ON part_case (part_id, vehicle_id)');

        $this->addSql('INSERT INTO part_case (id, part_id, vehicle_id) SELECT id, part_id, vehicle_id FROM _part_case ON CONFLICT  DO NOTHING');
        $this->addSql('DROP TABLE _part_case');

        $this->addSql('ALTER TABLE part_cross_part DROP tenant_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part_cross_part ADD tenant_id SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN part_cross_part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('DROP INDEX UNIQ_2A0E7894CE34BEC545317D1');
        $this->addSql('ALTER TABLE part_case ADD tenant_id SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN part_case.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a0e7894ce34bec545317d19033212a ON part_case (part_id, vehicle_id, tenant_id)');
    }
}
