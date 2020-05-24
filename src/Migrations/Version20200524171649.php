<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200524171649 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_part ADD part_uuid UUID DEFAULT NULL');
        // Data migration
        $this->addSql('UPDATE mc_part SET part_uuid = b.uuid FROM (SELECT id, part_id AS uuid FROM part) b WHERE b.id = part_id');
        // Data migration

        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT fk_2b65786f4ce34bec');
        $this->addSql('DROP INDEX idx_2b65786f4ce34bec');
        $this->addSql('ALTER TABLE mc_part DROP part_id');
        $this->addSql('ALTER TABLE mc_part RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE mc_part ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE INDEX IDX_C2E8923F4CE34BEC4E59C4629FF31636 ON stockpile (part_id, tenant, quantity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2E8923F4CE34BEC4E59C462 ON stockpile (part_id, tenant)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP INDEX IDX_C2E8923F4CE34BEC4E59C4629FF31636');
        $this->addSql('DROP INDEX UNIQ_C2E8923F4CE34BEC4E59C462');
        $this->addSql('ALTER TABLE mc_part ALTER part_id TYPE INT');
        $this->addSql('ALTER TABLE mc_part ALTER part_id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_part ALTER part_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN mc_part.part_id IS NULL');
        $this->addSql('ALTER TABLE 
          mc_part 
        ADD 
          CONSTRAINT fk_2b65786f4ce34bec FOREIGN KEY (part_id) REFERENCES part (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2b65786f4ce34bec ON mc_part (part_id)');
    }
}
