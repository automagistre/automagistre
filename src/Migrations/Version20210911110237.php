<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210911110237 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part_cross_part ADD tenant_id SMALLINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN part_cross_part.tenant_id IS \'(DC2Type:tenant_enum)\'');

        $this->addSql('UPDATE part_cross_part SET tenant_id = sub.tenant_id FROM (SELECT * FROM part_cross) sub WHERE sub.id = part_cross_part.part_cross_id');

        $this->addSql('ALTER TABLE part_cross_part ALTER tenant_id SET NOT NULL');

        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT fk_b98f499c70b9088c');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT fk_b98f499c4ce34bec');
        $this->addSql('DROP INDEX idx_b98f499c70b9088c');
        $this->addSql('DROP INDEX uniq_b98f499c4ce34bec');

        $this->addSql('DROP TABLE part_cross');
    }
}
